<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Buyer extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'buyers';

    protected $fillable = [
        'name', 'contact_info', 'address'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'buyer_id');
    }
}
