<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Planting extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'plantings';

    protected $fillable = [
        'field_id', 'crop', 'planting_date', 'expected_harvest_date', 'status'
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function harvests()
    {
        return $this->hasMany(Harvest::class, 'planting_id');
    }
}
