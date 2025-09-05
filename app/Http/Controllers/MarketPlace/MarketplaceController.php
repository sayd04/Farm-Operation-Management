<?php

namespace App\Http\Controllers\MarketPlace;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MarketplaceController extends Controller
{
    /**
     * Get available products for marketplace
     */
    public function products(Request $request): JsonResponse
    {
        $query = InventoryItem::where('category', InventoryItem::CATEGORY_PRODUCE)
                             ->where('quantity', '>', 0);

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        if (in_array($sortBy, ['name', 'price', 'quantity', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->paginate($request->get('per_page', 12));

        return response()->json($products);
    }

    /**
     * Get specific product details
     */
    public function showProduct(InventoryItem $product): JsonResponse
    {
        if ($product->category !== InventoryItem::CATEGORY_PRODUCE) {
            return response()->json([
                'message' => 'Product not found in marketplace'
            ], 404);
        }

        return response()->json([
            'product' => $product
        ]);
    }
}