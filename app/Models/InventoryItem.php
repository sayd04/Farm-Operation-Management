<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class InventoryItem extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'inventory_items';

    protected $fillable = [
        'category_id', 'name', 'quantity', 'unit', 'price'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
