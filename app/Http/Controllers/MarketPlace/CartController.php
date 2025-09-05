<?php

namespace App\Http\Controllers\MarketPlace;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Get cart items from session
     */
    public function index(Request $request): JsonResponse
    {
        $cart = session('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $itemId => $quantity) {
            $item = InventoryItem::find($itemId);
            if ($item && $item->category === InventoryItem::CATEGORY_PRODUCE) {
                $itemTotal = $item->price * $quantity;
                $cartItems[] = [
                    'item' => $item,
                    'quantity' => $quantity,
                    'total' => $itemTotal
                ];
                $total += $itemTotal;
            }
        }

        return response()->json([
            'cart_items' => $cartItems,
            'total' => $total,
            'count' => count($cartItems)
        ]);
    }

    /**
     * Add item to cart
     */
    public function addItem(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'inventory_item_id' => 'required|string',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $item = InventoryItem::find($request->inventory_item_id);
        
        if (!$item || $item->category !== InventoryItem::CATEGORY_PRODUCE) {
            return response()->json([
                'message' => 'Product not found in marketplace'
            ], 404);
        }

        if ($item->quantity < $request->quantity) {
            return response()->json([
                'message' => 'Insufficient stock available'
            ], 400);
        }

        $cart = session('cart', []);
        $cart[$item->id] = ($cart[$item->id] ?? 0) + $request->quantity;
        
        session(['cart' => $cart]);

        return response()->json([
            'message' => 'Item added to cart',
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateItem(Request $request, $itemId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $cart = session('cart', []);

        if (!isset($cart[$itemId])) {
            return response()->json([
                'message' => 'Item not found in cart'
            ], 404);
        }

        if ($request->quantity == 0) {
            unset($cart[$itemId]);
        } else {
            $cart[$itemId] = $request->quantity;
        }

        session(['cart' => $cart]);

        return response()->json([
            'message' => 'Cart updated successfully',
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem($itemId): JsonResponse
    {
        $cart = session('cart', []);

        if (!isset($cart[$itemId])) {
            return response()->json([
                'message' => 'Item not found in cart'
            ], 404);
        }

        unset($cart[$itemId]);
        session(['cart' => $cart]);

        return response()->json([
            'message' => 'Item removed from cart',
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Clear cart
     */
    public function clearCart(): JsonResponse
    {
        session()->forget('cart');

        return response()->json([
            'message' => 'Cart cleared successfully'
        ]);
    }
}