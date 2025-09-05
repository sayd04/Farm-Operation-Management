<?php

namespace App\Http\Controllers\MarketPlace;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = Order::with(['buyer', 'orderItems.inventoryItem']);
        
        if ($user->isBuyer()) {
            $query->where('buyer_id', $user->id);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Start transaction
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'buyer_id' => $request->user()->id,
                'status' => Order::STATUS_PENDING,
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            // Create order items
            foreach ($request->items as $itemData) {
                $inventoryItem = InventoryItem::find($itemData['inventory_item_id']);
                
                if (!$inventoryItem) {
                    throw new \Exception("Product not found: {$itemData['inventory_item_id']}");
                }

                if ($inventoryItem->category !== InventoryItem::CATEGORY_PRODUCE) {
                    throw new \Exception("Product not available in marketplace: {$inventoryItem->name}");
                }

                if ($inventoryItem->quantity < $itemData['quantity']) {
                    throw new \Exception("Insufficient stock for: {$inventoryItem->name}");
                }

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'inventory_item_id' => $inventoryItem->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $inventoryItem->price,
                ]);

                $totalAmount += $orderItem->total_price;

                // Reserve stock
                $inventoryItem->removeStock($itemData['quantity']);
            }

            // Update order total
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load(['orderItems.inventoryItem'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified order
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $order->buyer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->load(['buyer', 'orderItems.inventoryItem']);

        return response()->json([
            'order' => $order
        ]);
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        // Only admin or order owner can update
        $user = $request->user();
        if (!$user->isAdmin() && $order->buyer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buyers can only cancel pending orders
        if ($user->isBuyer() && $request->has('status')) {
            if ($request->status !== Order::STATUS_CANCELLED || !$order->canBeCancelled()) {
                return response()->json([
                    'message' => 'You can only cancel pending orders'
                ], 403);
            }
        }

        $order->update($request->only(['status']));

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order->load(['orderItems.inventoryItem'])
        ]);
    }

    /**
     * Confirm order (admin only)
     */
    public function confirm(Request $request, Order $order): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->markConfirmed();

        return response()->json([
            'message' => 'Order confirmed successfully',
            'order' => $order->load(['orderItems.inventoryItem'])
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $order->buyer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$order->canBeCancelled()) {
            return response()->json([
                'message' => 'Order cannot be cancelled at this stage'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Restore stock
            foreach ($order->orderItems as $orderItem) {
                $orderItem->inventoryItem->addStock($orderItem->quantity);
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);

            DB::commit();

            return response()->json([
                'message' => 'Order cancelled successfully',
                'order' => $order->load(['orderItems.inventoryItem'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ship order (admin only)
     */
    public function ship(Request $request, Order $order): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->markShipped();

        return response()->json([
            'message' => 'Order marked as shipped',
            'order' => $order->load(['orderItems.inventoryItem'])
        ]);
    }

    /**
     * Mark order as delivered (admin only)
     */
    public function deliver(Request $request, Order $order): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->markDelivered();

        return response()->json([
            'message' => 'Order marked as delivered',
            'order' => $order->load(['orderItems.inventoryItem'])
        ]);
    }
}