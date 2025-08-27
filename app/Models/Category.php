<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'categories';

    protected $fillable = [
        'name', 'description'
    ];

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'category_id');
    }
}
