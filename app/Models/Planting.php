<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Planting extends Model
{

    protected $fillable = [
        'field_id',
        'rice_variety_id',
        'crop_type',
        'planting_date',
        'expected_harvest_date',
        'actual_harvest_date',
        'status',
        'planting_method',
        'seed_rate',
        'area_planted',
        'season',
        'notes',
    ];

    protected $casts = [
        'planting_date' => 'datetime',
        'expected_harvest_date' => 'datetime',
        'actual_harvest_date' => 'datetime',
        'seed_rate' => 'decimal:2',
        'area_planted' => 'decimal:2',
    ];

    /**
     * Status constants
     */
    const STATUS_PLANTED = 'planted';
    const STATUS_GROWING = 'growing';
    const STATUS_READY = 'ready';
    const STATUS_HARVESTED = 'harvested';
    const STATUS_FAILED = 'failed';

    /**
     * Planting method constants
     */
    const METHOD_DIRECT_SEEDING = 'direct_seeding';
    const METHOD_TRANSPLANTING = 'transplanting';
    const METHOD_BROADCASTING = 'broadcasting';

    /**
     * Season constants
     */
    const SEASON_WET = 'wet';
    const SEASON_DRY = 'dry';

    /**
     * Get the field that owns the planting
     */
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the rice variety for this planting
     */
    public function riceVariety()
    {
        return $this->belongsTo(RiceVariety::class, 'rice_variety_id');
    }

    /**
     * Get the planting stages for this planting
     */
    public function plantingStages()
    {
        return $this->hasMany(PlantingStage::class);
    }

    /**
     * Get the harvests for this planting
     */
    public function harvests()
    {
        return $this->hasMany(Harvest::class);
    }

    /**
     * Get the tasks for this planting
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the expenses for this planting
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Check if planting is overdue
     */
    public function isOverdue(): bool
    {
        return $this->expected_harvest_date < Carbon::now() && 
               $this->status !== self::STATUS_HARVESTED;
    }

    /**
     * Get days until harvest
     */
    public function daysUntilHarvest(): int
    {
        return Carbon::now()->diffInDays($this->expected_harvest_date, false);
    }

    /**
     * Get total yield from harvests
     */
    public function getTotalYieldAttribute()
    {
        return $this->harvests()->sum('yield');
    }

    /**
     * Get current growth stage
     */
    public function getCurrentStage()
    {
        return $this->plantingStages()
                   ->whereIn('status', [PlantingStage::STATUS_IN_PROGRESS, PlantingStage::STATUS_PENDING])
                   ->with('riceGrowthStage')
                   ->orderBy('rice_growth_stage_id')
                   ->first();
    }

    /**
     * Get completed stages count
     */
    public function getCompletedStagesCount()
    {
        return $this->plantingStages()
                   ->where('status', PlantingStage::STATUS_COMPLETED)
                   ->count();
    }

    /**
     * Get overall completion percentage
     */
    public function getCompletionPercentage()
    {
        $totalStages = $this->plantingStages()->count();
        if ($totalStages === 0) return 0;

        $completedStages = $this->getCompletedStagesCount();
        return ($completedStages / $totalStages) * 100;
    }

    /**
     * Initialize growth stages for this planting
     */
    public function initializeGrowthStages()
    {
        $growthStages = RiceGrowthStage::active()->ordered()->get();
        
        foreach ($growthStages as $stage) {
            $this->plantingStages()->firstOrCreate([
                'rice_growth_stage_id' => $stage->id,
            ], [
                'status' => PlantingStage::STATUS_PENDING,
            ]);
        }
    }

    /**
     * Move to next growth stage
     */
    public function moveToNextStage()
    {
        $currentStage = $this->getCurrentStage();
        if ($currentStage) {
            $currentStage->complete();
            
            // Start the next stage if it exists
            $nextStageRecord = $this->plantingStages()
                                   ->where('rice_growth_stage_id', '>', $currentStage->rice_growth_stage_id)
                                   ->orderBy('rice_growth_stage_id')
                                   ->first();
            
            if ($nextStageRecord) {
                $nextStageRecord->start();
            }
        }
    }
}