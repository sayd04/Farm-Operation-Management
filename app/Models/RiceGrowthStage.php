<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiceGrowthStage extends Model
{
    protected $fillable = [
        'name',
        'stage_code',
        'description',
        'typical_duration_days',
        'order_sequence',
        'key_activities',
        'weather_requirements',
        'nutrient_requirements',
        'water_requirements',
        'common_problems',
        'is_active',
    ];

    protected $casts = [
        'key_activities' => 'json',
        'weather_requirements' => 'json',
        'nutrient_requirements' => 'json',
        'water_requirements' => 'json',
        'common_problems' => 'json',
        'is_active' => 'boolean',
    ];

    /**
     * Get planting stage records
     */
    public function plantingStages()
    {
        return $this->hasMany(PlantingStage::class);
    }

    /**
     * Scope for active stages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by sequence
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_sequence');
    }

    /**
     * Get the next growth stage
     */
    public function getNextStage()
    {
        return static::where('order_sequence', '>', $this->order_sequence)
                    ->orderBy('order_sequence')
                    ->first();
    }

    /**
     * Get the previous growth stage
     */
    public function getPreviousStage()
    {
        return static::where('order_sequence', '<', $this->order_sequence)
                    ->orderBy('order_sequence', 'desc')
                    ->first();
    }
}