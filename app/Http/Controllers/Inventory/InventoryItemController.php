<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of inventory items
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = InventoryItem::query();
        
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        
        // Apply filters
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('low_stock')) {
            $query->whereRaw('current_stock <= minimum_stock');
        }
        
        $inventoryItems = $query->orderBy('name')->get();
        
        return response()->json([
            'inventory_items' => $inventoryItems
        ]);
    }

    /**
     * Store a newly created inventory item
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:seeds,fertilizers,pesticides,equipment,tools,other',
            'unit' => 'required|string|in:kg,grams,pounds,liters,gallons,pieces,packets',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $inventoryItem = InventoryItem::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'unit' => $request->unit,
            'current_stock' => $request->current_stock,
            'minimum_stock' => $request->minimum_stock,
            'unit_price' => $request->unit_price,
            'supplier' => $request->supplier,
            'location' => $request->location,
            'expiry_date' => $request->expiry_date,
            'notes' => $request->notes,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Inventory item created successfully',
            'inventory_item' => $inventoryItem
        ], 201);
    }

    /**
     * Display the specified inventory item
     */
    public function show(Request $request, InventoryItem $inventoryItem): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $inventoryItem->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'inventory_item' => $inventoryItem
        ]);
    }

    /**
     * Update the specified inventory item
     */
    public function update(Request $request, InventoryItem $inventoryItem): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $inventoryItem->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|string|in:seeds,fertilizers,pesticides,equipment,tools,other',
            'unit' => 'sometimes|required|string|in:kg,grams,pounds,liters,gallons,pieces,packets',
            'current_stock' => 'sometimes|required|numeric|min:0',
            'minimum_stock' => 'sometimes|required|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $inventoryItem->update($request->only([
            'name', 'description', 'category', 'unit', 'current_stock',
            'minimum_stock', 'unit_price', 'supplier', 'location',
            'expiry_date', 'notes'
        ]));

        return response()->json([
            'message' => 'Inventory item updated successfully',
            'inventory_item' => $inventoryItem
        ]);
    }

    /**
     * Remove the specified inventory item
     */
    public function destroy(Request $request, InventoryItem $inventoryItem): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $inventoryItem->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        $inventoryItem->delete();

        return response()->json([
            'message' => 'Inventory item deleted successfully'
        ]);
    }

    /**
     * Update stock for an inventory item
     */
    public function updateStock(Request $request, InventoryItem $inventoryItem): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $inventoryItem->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'operation' => 'required|string|in:add,subtract,set',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $quantity = $request->quantity;
        
        switch ($request->operation) {
            case 'add':
                $inventoryItem->current_stock += $quantity;
                break;
            case 'subtract':
                $inventoryItem->current_stock -= $quantity;
                if ($inventoryItem->current_stock < 0) {
                    $inventoryItem->current_stock = 0;
                }
                break;
            case 'set':
                $inventoryItem->current_stock = $quantity;
                break;
        }

        $inventoryItem->save();

        return response()->json([
            'message' => 'Stock updated successfully',
            'inventory_item' => $inventoryItem
        ]);
    }

    /**
     * Get low stock items
     */
    public function lowStock(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = InventoryItem::query();
        
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }
        
        $lowStockItems = $query->whereRaw('current_stock <= minimum_stock')
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'low_stock_items' => $lowStockItems
        ]);
    }
}