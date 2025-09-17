<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    protected $table = 'buyers';

    protected $fillable = [
        'name', 'contact_info', 'address'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'buyer_id');
    }
}
