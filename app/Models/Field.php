<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Field extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'fields';

    protected $fillable = [
        'user_id', 'name', 'size', 'location', 'soil_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plantings()
    {
        return $this->hasMany(Planting::class, 'field_id');
    }

    public function weatherLogs()
    {
        return $this->hasMany(WeatherLog::class, 'field_id');
    }
}
