<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MarketplaceReview extends Model
{
    protected $fillable = [
        'order_id',
        'buyer_id',
        'farmer_id',
        'listing_id',
        'rating',
        'review_text',
        'review_categories',
        'images',
        'is_verified_purchase',
        'farmer_response',
        'farmer_responded_at',
        'is_featured',
    ];

    protected $casts = [
        'review_categories' => 'json',
        'images' => 'json',
        'is_verified_purchase' => 'boolean',
        'farmer_responded_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the order this review belongs to
     */
    public function order()
    {
        return $this->belongsTo(MarketplaceOrder::class, 'order_id');
    }

    /**
     * Get the buyer who wrote the review
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the farmer being reviewed
     */
    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    /**
     * Get the listing being reviewed
     */
    public function listing()
    {
        return $this->belongsTo(MarketplaceListing::class, 'listing_id');
    }

    /**
     * Scope for featured reviews
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for verified purchases
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    /**
     * Scope by rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope for reviews with farmer responses
     */
    public function scopeWithFarmerResponse($query)
    {
        return $query->whereNotNull('farmer_response');
    }

    /**
     * Add farmer response
     */
    public function addFarmerResponse(string $response)
    {
        $this->update([
            'farmer_response' => $response,
            'farmer_responded_at' => Carbon::now()
        ]);
    }

    /**
     * Check if farmer has responded
     */
    public function hasResponse(): bool
    {
        return !is_null($this->farmer_response);
    }

    /**
     * Get rating stars as array for UI
     */
    public function getRatingStarsAttribute()
    {
        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            $stars[] = $i <= $this->rating ? 'filled' : 'empty';
        }
        return $stars;
    }

    /**
     * Get review age in human readable format
     */
    public function getReviewAgeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if review is helpful (has good rating)
     */
    public function isHelpful(): bool
    {
        return $this->rating >= 4;
    }

    /**
     * Get category ratings if available
     */
    public function getCategoryRating(string $category): ?int
    {
        return $this->review_categories[$category] ?? null;
    }

    /**
     * Set category rating
     */
    public function setCategoryRating(string $category, int $rating)
    {
        $categories = $this->review_categories ?: [];
        $categories[$category] = $rating;
        $this->update(['review_categories' => $categories]);
    }
}