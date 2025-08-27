<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class WeatherLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'weather_logs';

    protected $fillable = [
        'field_id', 'temperature', 'humidity', 'rainfall', 'date'
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
