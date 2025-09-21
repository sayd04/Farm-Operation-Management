<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MarketplaceOrder extends Model
{
    protected $fillable = [
        'order_number',
        'buyer_id',
        'farmer_id',
        'listing_id',
        'quantity',
        'unit_price',
        'total_amount',
        'status',
        'payment_status',
        'delivery_method',
        'delivery_address',
        'buyer_notes',
        'farmer_notes',
        'requested_delivery_date',
        'confirmed_at',
        'delivered_at',
        'tracking_info',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'delivery_address' => 'json',
        'tracking_info' => 'json',
        'requested_delivery_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY_FOR_PICKUP = 'ready_for_pickup';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DISPUTED = 'disputed';

    /**
     * Payment status constants
     */
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_REFUNDED = 'refunded';
    const PAYMENT_FAILED = 'failed';

    /**
     * Delivery method constants
     */
    const DELIVERY_PICKUP = 'pickup';
    const DELIVERY_DELIVERY = 'delivery';
    const DELIVERY_SHIPPING = 'shipping';

    /**
     * Boot method to generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get the buyer
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the farmer
     */
    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    /**
     * Get the marketplace listing
     */
    public function listing()
    {
        return $this->belongsTo(MarketplaceListing::class, 'listing_id');
    }

    /**
     * Get the review for this order
     */
    public function review()
    {
        return $this->hasOne(MarketplaceReview::class, 'order_id');
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for confirmed orders
     */
    public function scopeConfirmed($query)
    {
        return $query->whereIn('status', [
            self::STATUS_CONFIRMED,
            self::STATUS_PREPARING,
            self::STATUS_READY_FOR_PICKUP,
            self::STATUS_IN_TRANSIT
        ]);
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    /**
     * Scope for farmer orders
     */
    public function scopeForFarmer($query, $farmerId)
    {
        return $query->where('farmer_id', $farmerId);
    }

    /**
     * Scope for buyer orders
     */
    public function scopeForBuyer($query, $buyerId)
    {
        return $query->where('buyer_id', $buyerId);
    }

    /**
     * Confirm order
     */
    public function confirm()
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => Carbon::now()
        ]);

        // Update listing quantity
        $this->listing->updateQuantityAfterOrder($this->quantity);
    }

    /**
     * Cancel order
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'farmer_notes' => $reason
        ]);
    }

    /**
     * Mark as preparing
     */
    public function markPreparing()
    {
        $this->update(['status' => self::STATUS_PREPARING]);
    }

    /**
     * Mark as ready for pickup
     */
    public function markReadyForPickup()
    {
        $this->update(['status' => self::STATUS_READY_FOR_PICKUP]);
    }

    /**
     * Mark as in transit
     */
    public function markInTransit($trackingInfo = null)
    {
        $updateData = ['status' => self::STATUS_IN_TRANSIT];
        if ($trackingInfo) {
            $updateData['tracking_info'] = $trackingInfo;
        }
        $this->update($updateData);
    }

    /**
     * Mark as delivered
     */
    public function markDelivered()
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivered_at' => Carbon::now()
        ]);
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED
        ]);
    }

    /**
     * Check if order can be reviewed
     */
    public function canBeReviewed(): bool
    {
        return $this->status === self::STATUS_DELIVERED && !$this->review;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending Confirmation',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_PREPARING => 'Preparing Order',
            self::STATUS_READY_FOR_PICKUP => 'Ready for Pickup',
            self::STATUS_IN_TRANSIT => 'In Transit',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_DISPUTED => 'Disputed',
            default => 'Unknown'
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_PREPARING => 'purple',
            self::STATUS_READY_FOR_PICKUP => 'indigo',
            self::STATUS_IN_TRANSIT => 'cyan',
            self::STATUS_DELIVERED => 'green',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_DISPUTED => 'orange',
            default => 'gray'
        };
    }

    /**
     * Get delivery method display name
     */
    public function getDeliveryMethodDisplayAttribute()
    {
        return match($this->delivery_method) {
            self::DELIVERY_PICKUP => 'Pickup',
            self::DELIVERY_DELIVERY => 'Delivery',
            self::DELIVERY_SHIPPING => 'Shipping',
            default => 'Unknown'
        };
    }

    /**
     * Get payment status display name
     */
    public function getPaymentStatusDisplayAttribute()
    {
        return match($this->payment_status) {
            self::PAYMENT_PENDING => 'Payment Pending',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_REFUNDED => 'Refunded',
            self::PAYMENT_FAILED => 'Payment Failed',
            default => 'Unknown'
        };
    }

    /**
     * Calculate estimated delivery date
     */
    public function getEstimatedDeliveryDateAttribute()
    {
        if ($this->requested_delivery_date) {
            return $this->requested_delivery_date;
        }

        $baseDays = match($this->delivery_method) {
            self::DELIVERY_PICKUP => 1,
            self::DELIVERY_DELIVERY => 2,
            self::DELIVERY_SHIPPING => 5,
            default => 3
        };

        return $this->confirmed_at ? 
               $this->confirmed_at->addDays($baseDays) : 
               Carbon::now()->addDays($baseDays);
    }
}