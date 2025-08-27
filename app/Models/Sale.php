<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Sale extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'sales';

    protected $fillable = [
        'harvest_id', 'buyer_id', 'quantity', 'price', 'sale_date'
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }
}
