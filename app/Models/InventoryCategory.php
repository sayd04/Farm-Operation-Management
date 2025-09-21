<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryCategory extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'icon',
        'color',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
        'is_active' => 'boolean',
    ];

    /**
     * Get inventory items in this category
     */
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'category_id');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get item count for this category
     */
    public function getItemCountAttribute()
    {
        return $this->inventoryItems()->count();
    }

    /**
     * Get low stock items count
     */
    public function getLowStockItemsCountAttribute()
    {
        return $this->inventoryItems()
                   ->whereRaw('quantity <= min_stock')
                   ->count();
    }

    /**
     * Get total value of items in this category
     */
    public function getTotalValueAttribute()
    {
        return $this->inventoryItems()
                   ->selectRaw('SUM(quantity * cost_per_unit) as total')
                   ->value('total') ?: 0;
    }
}