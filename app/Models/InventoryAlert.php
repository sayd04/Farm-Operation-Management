<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InventoryAlert extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'alert_type',
        'severity',
        'title',
        'message',
        'alert_data',
        'recommendations',
        'is_active',
        'is_read',
        'expires_at',
    ];

    protected $casts = [
        'alert_data' => 'json',
        'recommendations' => 'json',
        'is_active' => 'boolean',
        'is_read' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Alert type constants
     */
    const TYPE_LOW_STOCK = 'low_stock';
    const TYPE_OUT_OF_STOCK = 'out_of_stock';
    const TYPE_EXPIRING = 'expiring';
    const TYPE_EXPIRED = 'expired';
    const TYPE_REORDER_POINT = 'reorder_point';

    /**
     * Severity constants
     */
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Get the inventory item
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Scope for active alerts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', Carbon::now());
                    });
    }

    /**
     * Scope for unread alerts
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope by alert type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('alert_type', $type);
    }

    /**
     * Mark alert as read
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Deactivate alert
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Check if alert is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && Carbon::now()->gt($this->expires_at);
    }

    /**
     * Get severity color for UI
     */
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            self::SEVERITY_LOW => 'blue',
            self::SEVERITY_MEDIUM => 'yellow',
            self::SEVERITY_HIGH => 'orange',
            self::SEVERITY_CRITICAL => 'red',
            default => 'gray'
        };
    }

    /**
     * Get alert icon
     */
    public function getAlertIconAttribute()
    {
        return match($this->alert_type) {
            self::TYPE_LOW_STOCK => 'exclamation-triangle',
            self::TYPE_OUT_OF_STOCK => 'x-circle',
            self::TYPE_EXPIRING => 'clock',
            self::TYPE_EXPIRED => 'ban',
            self::TYPE_REORDER_POINT => 'shopping-cart',
            default => 'bell'
        };
    }

    /**
     * Get alert type display name
     */
    public function getAlertTypeDisplayAttribute()
    {
        return match($this->alert_type) {
            self::TYPE_LOW_STOCK => 'Low Stock',
            self::TYPE_OUT_OF_STOCK => 'Out of Stock',
            self::TYPE_EXPIRING => 'Expiring Soon',
            self::TYPE_EXPIRED => 'Expired',
            self::TYPE_REORDER_POINT => 'Reorder Point Reached',
            default => 'Unknown'
        };
    }
}