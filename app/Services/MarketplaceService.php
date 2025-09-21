<?php

namespace App\Services;

use App\Models\MarketplaceListing;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceReview;
use App\Models\User;
use App\Models\Harvest;
use App\Models\RiceVariety;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class MarketplaceService
{
    /**
     * Search marketplace listings with filters
     */
    public function searchListings(array $filters = []): Builder
    {
        $query = MarketplaceListing::query()
            ->with(['farmer', 'riceVariety', 'reviews'])
            ->active();

        // Filter by rice variety
        if (!empty($filters['rice_variety_id'])) {
            $query->where('rice_variety_id', $filters['rice_variety_id']);
        }

        // Filter by quality grade
        if (!empty($filters['quality_grade'])) {
            $query->where('quality_grade', $filters['quality_grade']);
        }

        // Filter by price range
        if (!empty($filters['min_price'])) {
            $query->where('price_per_kg', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price_per_kg', '<=', $filters['max_price']);
        }

        // Filter by location
        if (!empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        // Filter by minimum quantity available
        if (!empty($filters['min_quantity'])) {
            $query->where('quantity_available', '>=', $filters['min_quantity']);
        }

        // Filter by delivery options
        if (!empty($filters['delivery_method'])) {
            $query->whereJsonContains('delivery_options', $filters['delivery_method']);
        }

        // Search in title and description
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort options
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price_per_kg', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_per_kg', 'desc');
                break;
            case 'rating':
                // This would need a subquery for average rating
                $query->withAvg('reviews', 'rating')
                      ->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }

    /**
     * Create marketplace listing from harvest
     */
    public function createListingFromHarvest(Harvest $harvest, array $listingData): MarketplaceListing
    {
        $planting = $harvest->planting;
        $field = $planting->field;
        $farmer = $field->user;

        $listing = MarketplaceListing::create([
            'farmer_id' => $farmer->id,
            'harvest_id' => $harvest->id,
            'rice_variety_id' => $planting->rice_variety_id,
            'title' => $listingData['title'] ?? "{$planting->riceVariety->name} Rice - {$harvest->quality} Quality",
            'description' => $listingData['description'] ?? "Fresh {$planting->riceVariety->name} rice harvested from our farm.",
            'quantity_available' => $listingData['quantity_available'] ?? $harvest->yield,
            'price_per_kg' => $listingData['price_per_kg'],
            'quality_grade' => $this->mapHarvestQualityToGrade($harvest->quality),
            'moisture_content' => $listingData['moisture_content'] ?? null,
            'harvest_details' => [
                'harvest_date' => $harvest->harvest_date,
                'field_name' => $field->name,
                'farm_name' => $field->farm->name,
                'planting_method' => $planting->planting_method,
                'season' => $planting->season,
                'variety_maturity_days' => $planting->riceVariety->maturity_days,
            ],
            'location' => $listingData['location'] ?? $field->farm->location,
            'delivery_options' => $listingData['delivery_options'] ?? ['pickup'],
            'minimum_order' => $listingData['minimum_order'] ?? 25.0,
            'terms_conditions' => $listingData['terms_conditions'] ?? null,
            'status' => MarketplaceListing::STATUS_DRAFT,
        ]);

        return $listing;
    }

    /**
     * Create order for a listing
     */
    public function createOrder(MarketplaceListing $listing, User $buyer, array $orderData): MarketplaceOrder
    {
        // Validate order quantity
        if ($orderData['quantity'] > $listing->remaining_quantity) {
            throw new \Exception('Requested quantity exceeds available stock');
        }

        if ($orderData['quantity'] < $listing->minimum_order) {
            throw new \Exception("Minimum order quantity is {$listing->minimum_order} kg");
        }

        $order = MarketplaceOrder::create([
            'buyer_id' => $buyer->id,
            'farmer_id' => $listing->farmer_id,
            'listing_id' => $listing->id,
            'quantity' => $orderData['quantity'],
            'unit_price' => $listing->price_per_kg,
            'total_amount' => $orderData['quantity'] * $listing->price_per_kg,
            'delivery_method' => $orderData['delivery_method'],
            'delivery_address' => $orderData['delivery_address'] ?? null,
            'buyer_notes' => $orderData['buyer_notes'] ?? null,
            'requested_delivery_date' => $orderData['requested_delivery_date'] ?? null,
        ]);

        // Send notification to farmer (implement notification system)
        // $this->notifyFarmerOfNewOrder($order);

        return $order;
    }

    /**
     * Get marketplace statistics
     */
    public function getMarketplaceStats(): array
    {
        return [
            'total_listings' => MarketplaceListing::active()->count(),
            'total_farmers' => MarketplaceListing::distinct('farmer_id')->count(),
            'total_orders' => MarketplaceOrder::count(),
            'total_sales_value' => MarketplaceOrder::whereIn('status', ['delivered'])->sum('total_amount'),
            'average_rating' => MarketplaceReview::avg('rating'),
            'popular_varieties' => $this->getPopularRiceVarieties(),
            'recent_activity' => $this->getRecentActivity(),
        ];
    }

    /**
     * Get farmer dashboard statistics
     */
    public function getFarmerStats(User $farmer): array
    {
        return [
            'total_listings' => MarketplaceListing::where('farmer_id', $farmer->id)->count(),
            'active_listings' => MarketplaceListing::where('farmer_id', $farmer->id)->active()->count(),
            'total_orders' => MarketplaceOrder::where('farmer_id', $farmer->id)->count(),
            'pending_orders' => MarketplaceOrder::where('farmer_id', $farmer->id)->pending()->count(),
            'total_sales' => MarketplaceOrder::where('farmer_id', $farmer->id)
                                           ->whereIn('status', ['delivered'])
                                           ->sum('total_amount'),
            'average_rating' => MarketplaceReview::where('farmer_id', $farmer->id)->avg('rating'),
            'total_reviews' => MarketplaceReview::where('farmer_id', $farmer->id)->count(),
            'quantity_sold' => MarketplaceOrder::where('farmer_id', $farmer->id)
                                             ->whereIn('status', ['delivered'])
                                             ->sum('quantity'),
        ];
    }

    /**
     * Get buyer dashboard statistics
     */
    public function getBuyerStats(User $buyer): array
    {
        return [
            'total_orders' => MarketplaceOrder::where('buyer_id', $buyer->id)->count(),
            'completed_orders' => MarketplaceOrder::where('buyer_id', $buyer->id)->completed()->count(),
            'total_spent' => MarketplaceOrder::where('buyer_id', $buyer->id)
                                           ->whereIn('status', ['delivered'])
                                           ->sum('total_amount'),
            'total_quantity_purchased' => MarketplaceOrder::where('buyer_id', $buyer->id)
                                                        ->whereIn('status', ['delivered'])
                                                        ->sum('quantity'),
            'reviews_given' => MarketplaceReview::where('buyer_id', $buyer->id)->count(),
            'favorite_variety' => $this->getBuyerFavoriteVariety($buyer),
        ];
    }

    /**
     * Get recommended listings for a buyer
     */
    public function getRecommendedListings(User $buyer, int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        // Get buyer's order history to understand preferences
        $previousOrders = MarketplaceOrder::where('buyer_id', $buyer->id)
                                        ->with('listing.riceVariety')
                                        ->get();

        $preferredVarieties = $previousOrders->pluck('listing.rice_variety_id')->unique();
        $averagePrice = $previousOrders->avg('unit_price');

        $query = MarketplaceListing::active()
                                  ->with(['farmer', 'riceVariety', 'reviews']);

        if ($preferredVarieties->isNotEmpty()) {
            // Prioritize preferred varieties
            $query->whereIn('rice_variety_id', $preferredVarieties);
        }

        if ($averagePrice) {
            // Show listings within price range
            $priceRange = $averagePrice * 0.2; // 20% variance
            $query->whereBetween('price_per_kg', [
                $averagePrice - $priceRange,
                $averagePrice + $priceRange
            ]);
        }

        return $query->orderBy('is_featured', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Map harvest quality to marketplace grade
     */
    protected function mapHarvestQualityToGrade(string $harvestQuality): string
    {
        return match($harvestQuality) {
            'excellent' => MarketplaceListing::QUALITY_PREMIUM,
            'good' => MarketplaceListing::QUALITY_GRADE_A,
            'average' => MarketplaceListing::QUALITY_GRADE_B,
            'poor' => MarketplaceListing::QUALITY_STANDARD,
            default => MarketplaceListing::QUALITY_STANDARD,
        };
    }

    /**
     * Get popular rice varieties
     */
    protected function getPopularRiceVarieties(): array
    {
        return MarketplaceOrder::join('marketplace_listings', 'marketplace_orders.listing_id', '=', 'marketplace_listings.id')
                              ->join('rice_varieties', 'marketplace_listings.rice_variety_id', '=', 'rice_varieties.id')
                              ->selectRaw('rice_varieties.name, rice_varieties.id, COUNT(*) as order_count, SUM(marketplace_orders.quantity) as total_quantity')
                              ->groupBy('rice_varieties.id', 'rice_varieties.name')
                              ->orderBy('order_count', 'desc')
                              ->limit(5)
                              ->get()
                              ->toArray();
    }

    /**
     * Get recent marketplace activity
     */
    protected function getRecentActivity(): array
    {
        $recentOrders = MarketplaceOrder::with(['buyer', 'listing'])
                                       ->latest()
                                       ->limit(10)
                                       ->get();

        $recentListings = MarketplaceListing::with(['farmer', 'riceVariety'])
                                           ->where('status', MarketplaceListing::STATUS_ACTIVE)
                                           ->latest()
                                           ->limit(10)
                                           ->get();

        return [
            'recent_orders' => $recentOrders,
            'recent_listings' => $recentListings,
        ];
    }

    /**
     * Get buyer's favorite rice variety
     */
    protected function getBuyerFavoriteVariety(User $buyer): ?array
    {
        $favoriteVariety = MarketplaceOrder::where('buyer_id', $buyer->id)
                                         ->join('marketplace_listings', 'marketplace_orders.listing_id', '=', 'marketplace_listings.id')
                                         ->join('rice_varieties', 'marketplace_listings.rice_variety_id', '=', 'rice_varieties.id')
                                         ->selectRaw('rice_varieties.name, rice_varieties.id, COUNT(*) as order_count')
                                         ->groupBy('rice_varieties.id', 'rice_varieties.name')
                                         ->orderBy('order_count', 'desc')
                                         ->first();

        return $favoriteVariety ? $favoriteVariety->toArray() : null;
    }

    /**
     * Process order confirmation
     */
    public function confirmOrder(MarketplaceOrder $order): bool
    {
        try {
            $order->confirm();
            
            // Send confirmation notifications
            // $this->notifyBuyerOfConfirmation($order);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Order confirmation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get quality recommendations based on rice variety
     */
    public function getQualityRecommendations(RiceVariety $variety): array
    {
        return [
            'optimal_moisture' => '14-15%',
            'storage_conditions' => 'Store in cool, dry place away from direct sunlight',
            'shelf_life' => '12-18 months when properly stored',
            'cooking_tips' => $this->getCookingTips($variety),
            'nutritional_benefits' => $this->getNutritionalBenefits($variety),
        ];
    }

    /**
     * Get cooking tips for rice variety
     */
    protected function getCookingTips(RiceVariety $variety): array
    {
        $tips = [
            'water_ratio' => '1:1.5 (rice:water)',
            'cooking_time' => '18-20 minutes',
            'preparation' => 'Rinse rice 2-3 times before cooking',
        ];

        // Variety-specific tips
        if (str_contains(strtolower($variety->name), 'jasmine')) {
            $tips['special_note'] = 'Fragrant rice - avoid over-washing to preserve aroma';
            $tips['water_ratio'] = '1:1.25 (rice:water)';
        }

        return $tips;
    }

    /**
     * Get nutritional benefits for rice variety
     */
    protected function getNutritionalBenefits(RiceVariety $variety): array
    {
        return [
            'carbohydrates' => 'Good source of energy',
            'protein' => 'Contains essential amino acids',
            'vitamins' => 'B vitamins, especially thiamine',
            'minerals' => 'Iron, magnesium, phosphorus',
            'fiber' => 'Aids in digestion',
        ];
    }
}