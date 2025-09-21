<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MarketplaceListing extends Model
{
    protected $fillable = [
        'farmer_id',
        'harvest_id',
        'rice_variety_id',
        'title',
        'description',
        'quantity_available',
        'price_per_kg',
        'unit',
        'quality_grade',
        'moisture_content',
        'harvest_details',
        'images',
        'status',
        'available_from',
        'available_until',
        'location',
        'delivery_options',
        'minimum_order',
        'terms_conditions',
        'is_featured',
        'views_count',
    ];

    protected $casts = [
        'harvest_details' => 'json',
        'images' => 'json',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'delivery_options' => 'json',
        'quantity_available' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'moisture_content' => 'decimal:2',
        'minimum_order' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    /**
     * Status constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_SOLD_OUT = 'sold_out';
    const STATUS_EXPIRED = 'expired';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Quality grade constants
     */
    const QUALITY_PREMIUM = 'premium';
    const QUALITY_GRADE_A = 'grade_a';
    const QUALITY_GRADE_B = 'grade_b';
    const QUALITY_STANDARD = 'standard';

    /**
     * Get the farmer who owns this listing
     */
    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    /**
     * Get the harvest this listing is based on
     */
    public function harvest()
    {
        return $this->belongsTo(Harvest::class);
    }

    /**
     * Get the rice variety
     */
    public function riceVariety()
    {
        return $this->belongsTo(RiceVariety::class);
    }

    /**
     * Get marketplace orders for this listing
     */
    public function marketplaceOrders()
    {
        return $this->hasMany(MarketplaceOrder::class, 'listing_id');
    }

    /**
     * Get reviews for this listing
     */
    public function reviews()
    {
        return $this->hasMany(MarketplaceReview::class, 'listing_id');
    }

    /**
     * Scope for active listings
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where('quantity_available', '>', 0)
                    ->where(function($q) {
                        $q->whereNull('available_until')
                          ->orWhere('available_until', '>', Carbon::now());
                    });
    }

    /**
     * Scope for featured listings
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope by rice variety
     */
    public function scopeByVariety($query, $varietyId)
    {
        return $query->where('rice_variety_id', $varietyId);
    }

    /**
     * Scope by quality grade
     */
    public function scopeByQuality($query, $quality)
    {
        return $query->where('quality_grade', $quality);
    }

    /**
     * Scope by price range
     */
    public function scopeByPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->where('price_per_kg', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('price_per_kg', '<=', $maxPrice);
        }
        return $query;
    }

    /**
     * Scope by location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Check if listing is available
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               $this->quantity_available > 0 &&
               (!$this->available_until || Carbon::now()->lte($this->available_until));
    }

    /**
     * Check if listing is expired
     */
    public function isExpired(): bool
    {
        return $this->available_until && Carbon::now()->gt($this->available_until);
    }

    /**
     * Get quantity sold
     */
    public function getQuantitySoldAttribute()
    {
        return $this->marketplaceOrders()
                   ->whereIn('status', ['confirmed', 'preparing', 'ready_for_pickup', 'in_transit', 'delivered'])
                   ->sum('quantity');
    }

    /**
     * Get remaining quantity
     */
    public function getRemainingQuantityAttribute()
    {
        return $this->quantity_available - $this->quantity_sold;
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }

    /**
     * Get total reviews count
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Update quantity after order
     */
    public function updateQuantityAfterOrder(float $orderedQuantity)
    {
        $newQuantity = $this->quantity_available - $orderedQuantity;
        $this->update([
            'quantity_available' => max(0, $newQuantity),
            'status' => $newQuantity <= 0 ? self::STATUS_SOLD_OUT : $this->status
        ]);
    }

    /**
     * Activate listing
     */
    public function activate()
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'available_from' => $this->available_from ?: Carbon::now()
        ]);
    }

    /**
     * Suspend listing
     */
    public function suspend()
    {
        $this->update(['status' => self::STATUS_SUSPENDED]);
    }

    /**
     * Get quality grade display name
     */
    public function getQualityGradeDisplayAttribute()
    {
        return match($this->quality_grade) {
            self::QUALITY_PREMIUM => 'Premium',
            self::QUALITY_GRADE_A => 'Grade A',
            self::QUALITY_GRADE_B => 'Grade B',
            self::QUALITY_STANDARD => 'Standard',
            default => 'Unknown'
        };
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_SOLD_OUT => 'Sold Out',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_SUSPENDED => 'Suspended',
            default => 'Unknown'
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_ACTIVE => 'green',
            self::STATUS_SOLD_OUT => 'red',
            self::STATUS_EXPIRED => 'orange',
            self::STATUS_SUSPENDED => 'yellow',
            default => 'gray'
        };
    }
}