<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InventoryItem extends Model
{

    protected $fillable = [
        'name',
        'category',
        'category_id',
        'sku',
        'brand',
        'quantity',
        'price',
        'cost_per_unit',
        'unit',
        'min_stock',
        'reorder_point',
        'max_stock',
        'expiry_date',
        'shelf_life_days',
        'storage_requirements',
        'usage_instructions',
        'safety_data',
        'supplier',
        'supplier_contact',
        'lead_time_days',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'max_stock' => 'decimal:2',
        'expiry_date' => 'date',
        'storage_requirements' => 'json',
        'usage_instructions' => 'json',
        'safety_data' => 'json',
        'lead_time_days' => 'decimal:1',
        'is_active' => 'boolean',
        'metadata' => 'json',
    ];

    /**
     * Category constants
     */
    const CATEGORY_SEEDS = 'seeds';
    const CATEGORY_FERTILIZER = 'fertilizer';
    const CATEGORY_PESTICIDE = 'pesticide';
    const CATEGORY_EQUIPMENT = 'equipment';
    const CATEGORY_PRODUCE = 'produce';

    /**
     * Get the inventory category
     */
    public function inventoryCategory()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    /**
     * Get inventory transactions
     */
    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Get inventory alerts
     */
    public function alerts()
    {
        return $this->hasMany(InventoryAlert::class);
    }

    /**
     * Get the order items for this inventory item
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if item is low stock
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_stock;
    }

    /**
     * Check if item has reached reorder point
     */
    public function hasReachedReorderPoint(): bool
    {
        return $this->reorder_point && $this->quantity <= $this->reorder_point;
    }

    /**
     * Check if item is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }

    /**
     * Check if item is overstocked
     */
    public function isOverstocked(): bool
    {
        return $this->max_stock && $this->quantity > $this->max_stock;
    }

    /**
     * Check if item is expiring soon (within 30 days)
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->lte(Carbon::now()->addDays($days));
    }

    /**
     * Check if item is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->lt(Carbon::now());
    }

    /**
     * Add stock with transaction logging
     */
    public function addStock($quantity, $unitCost = null, $userId = null, $notes = null, $referenceType = null, $referenceId = null)
    {
        $this->increment('quantity', $quantity);
        
        // Log transaction
        $this->transactions()->create([
            'user_id' => $userId ?: auth()->id(),
            'transaction_type' => InventoryTransaction::TYPE_IN,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $unitCost ? $quantity * $unitCost : null,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'transaction_date' => Carbon::now(),
        ]);
    }

    /**
     * Remove stock with transaction logging
     */
    public function removeStock($quantity, $userId = null, $notes = null, $referenceType = null, $referenceId = null)
    {
        if ($this->quantity >= $quantity) {
            $this->decrement('quantity', $quantity);
            
            // Log transaction
            $this->transactions()->create([
                'user_id' => $userId ?: auth()->id(),
                'transaction_type' => InventoryTransaction::TYPE_OUT,
                'quantity' => -$quantity, // Negative for outgoing
                'unit_cost' => $this->cost_per_unit,
                'total_cost' => $this->cost_per_unit ? $quantity * $this->cost_per_unit : null,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'transaction_date' => Carbon::now(),
            ]);
            
            return true;
        }
        return false;
    }

    /**
     * Adjust stock with transaction logging
     */
    public function adjustStock($newQuantity, $userId = null, $notes = null)
    {
        $difference = $newQuantity - $this->quantity;
        $this->update(['quantity' => $newQuantity]);
        
        // Log transaction
        $this->transactions()->create([
            'user_id' => $userId ?: auth()->id(),
            'transaction_type' => InventoryTransaction::TYPE_ADJUSTMENT,
            'quantity' => $difference,
            'notes' => $notes ?: 'Stock adjustment',
            'transaction_date' => Carbon::now(),
        ]);
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute()
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        }
        return 'in_stock';
    }
}