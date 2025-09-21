<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'location',
        'description',
        'total_area',
        'cultivated_area',
        'soil_type',
        'soil_ph',
        'water_source',
        'irrigation_type',
        'primary_rice_variety_id',
        'farm_coordinates',
        'elevation',
        'slope',
        'drainage_system',
        'is_setup_complete',
    ];

    protected $casts = [
        'farm_coordinates' => 'json',
        'total_area' => 'decimal:2',
        'cultivated_area' => 'decimal:2',
        'soil_ph' => 'decimal:1',
        'elevation' => 'decimal:2',
        'slope' => 'decimal:2',
        'is_setup_complete' => 'boolean',
    ];

    /**
     * Soil type constants
     */
    const SOIL_CLAY = 'clay';
    const SOIL_LOAM = 'loam';
    const SOIL_SANDY_LOAM = 'sandy_loam';
    const SOIL_CLAY_LOAM = 'clay_loam';
    const SOIL_SILT_LOAM = 'silt_loam';

    /**
     * Water source constants
     */
    const WATER_RAINFALL = 'rainfall';
    const WATER_IRRIGATION = 'irrigation';
    const WATER_RIVER = 'river';
    const WATER_WELL = 'well';
    const WATER_POND = 'pond';

    /**
     * Irrigation type constants
     */
    const IRRIGATION_FLOOD = 'flood';
    const IRRIGATION_SPRINKLER = 'sprinkler';
    const IRRIGATION_DRIP = 'drip';
    const IRRIGATION_FURROW = 'furrow';

    /**
     * Drainage system constants
     */
    const DRAINAGE_GOOD = 'good';
    const DRAINAGE_MODERATE = 'moderate';
    const DRAINAGE_POOR = 'poor';

    /**
     * Get the farmer that owns the farm
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the primary rice variety
     */
    public function primaryRiceVariety()
    {
        return $this->belongsTo(RiceVariety::class, 'primary_rice_variety_id');
    }

    /**
     * Get the fields in this farm
     */
    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    /**
     * Get all plantings in this farm
     */
    public function plantings()
    {
        return $this->hasManyThrough(Planting::class, Field::class);
    }

    /**
     * Check if farm setup is complete
     */
    public function isSetupComplete(): bool
    {
        return $this->is_setup_complete && 
               !is_null($this->total_area) && 
               !is_null($this->soil_type) && 
               !is_null($this->water_source) && 
               !is_null($this->primary_rice_variety_id);
    }

    /**
     * Get the utilization percentage
     */
    public function getUtilizationPercentageAttribute()
    {
        if ($this->total_area <= 0) return 0;
        return ($this->cultivated_area / $this->total_area) * 100;
    }

    /**
     * Scope for completed setups
     */
    public function scopeSetupComplete($query)
    {
        return $query->where('is_setup_complete', true);
    }
}
