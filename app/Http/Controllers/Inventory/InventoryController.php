<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items
     */
    public function index(Request $request): JsonResponse
    {
        $query = InventoryItem::query();

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by stock status
        if ($request->has('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereRaw('quantity <= min_stock');
                    break;
                case 'out':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'in_stock':
                    $query->whereRaw('quantity > min_stock');
                    break;
            }
        }

        $items = $query->orderBy('name')->get();

        return response()->json([
            'inventory_items' => $items
        ]);
    }

    /**
     * Store a newly created inventory item
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|in:seeds,fertilizer,pesticide,equipment,produce',
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $item = InventoryItem::create($request->all());

        return response()->json([
            'message' => 'Inventory item created successfully',
            'item' => $item
        ], 201);
    }

    /**
     * Display the specified inventory item
     */
    public function show(InventoryItem $item): JsonResponse
    {
        return response()->json([
            'item' => $item
        ]);
    }

    /**
     * Update the specified inventory item
     */
    public function update(Request $request, InventoryItem $item): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|in:seeds,fertilizer,pesticide,equipment,produce',
            'quantity' => 'sometimes|numeric|min:0',
            'price' => 'sometimes|numeric|min:0',
            'unit' => 'sometimes|string|max:50',
            'min_stock' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $item->update($request->only([
            'name', 'category', 'quantity', 'price', 'unit', 'min_stock'
        ]));

        return response()->json([
            'message' => 'Inventory item updated successfully',
            'item' => $item
        ]);
    }

    /**
     * Remove the specified inventory item
     */
    public function destroy(InventoryItem $item): JsonResponse
    {
        $item->delete();

        return response()->json([
            'message' => 'Inventory item deleted successfully'
        ]);
    }

    /**
     * Add stock to inventory item
     */
    public function addStock(Request $request, InventoryItem $item): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $item->addStock($request->quantity);

        return response()->json([
            'message' => 'Stock added successfully',
            'item' => $item->fresh()
        ]);
    }

    /**
     * Remove stock from inventory item
     */
    public function removeStock(Request $request, InventoryItem $item): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!$item->removeStock($request->quantity)) {
            return response()->json([
                'message' => 'Insufficient stock available'
            ], 400);
        }

        return response()->json([
            'message' => 'Stock removed successfully',
            'item' => $item->fresh()
        ]);
    }

    /**
     * Get low stock alerts
     */
    public function lowStockAlerts(): JsonResponse
    {
        $lowStockItems = InventoryItem::whereRaw('quantity <= min_stock')->get();

        return response()->json([
            'low_stock_items' => $lowStockItems,
            'count' => $lowStockItems->count()
        ]);
    }
}