<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiceVariety extends Model
{
    protected $fillable = [
        'name',
        'variety_code',
        'description',
        'maturity_days',
        'average_yield_per_hectare',
        'season',
        'grain_type',
        'resistance_level',
        'characteristics',
        'is_active',
    ];

    protected $casts = [
        'characteristics' => 'json',
        'is_active' => 'boolean',
        'average_yield_per_hectare' => 'decimal:2',
    ];

    /**
     * Season constants
     */
    const SEASON_WET = 'wet';
    const SEASON_DRY = 'dry';
    const SEASON_BOTH = 'both';

    /**
     * Grain type constants
     */
    const GRAIN_LONG = 'long';
    const GRAIN_MEDIUM = 'medium';
    const GRAIN_SHORT = 'short';

    /**
     * Resistance level constants
     */
    const RESISTANCE_HIGH = 'high';
    const RESISTANCE_MEDIUM = 'medium';
    const RESISTANCE_LOW = 'low';

    /**
     * Get plantings using this rice variety
     */
    public function plantings()
    {
        return $this->hasMany(Planting::class);
    }

    /**
     * Get farms using this rice variety
     */
    public function farms()
    {
        return $this->hasMany(Farm::class, 'primary_rice_variety_id');
    }

    /**
     * Scope for active varieties
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by season
     */
    public function scopeBySeason($query, $season)
    {
        return $query->where('season', $season)->orWhere('season', self::SEASON_BOTH);
    }

    /**
     * Get estimated harvest date based on planting date
     */
    public function getEstimatedHarvestDate($plantingDate)
    {
        return \Carbon\Carbon::parse($plantingDate)->addDays($this->maturity_days);
    }
}