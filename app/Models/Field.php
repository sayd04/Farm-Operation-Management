<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Field extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'fields';

    protected $fillable = [
        'user_id',
        'location',
        'soil_type',
        'size',
    ];

    protected $casts = [
        'location' => 'array',
        'size' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the field
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plantings for this field
     */
    public function plantings()
    {
        return $this->hasMany(Planting::class);
    }

    /**
     * Get the weather logs for this field
     */
    public function weatherLogs()
    {
        return $this->hasMany(WeatherLog::class);
    }

    /**
     * Get the latest weather log
     */
    public function latestWeather()
    {
        return $this->hasOne(WeatherLog::class)->latest('recorded_at');
    }
}