<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Harvest extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'harvests';

    protected $fillable = [
        'planting_id', 'quantity', 'harvest_date', 'quality'
    ];

    public function planting()
    {
        return $this->belongsTo(Planting::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'harvest_id');
    }
}
