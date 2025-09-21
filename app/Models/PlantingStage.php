<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PlantingStage extends Model
{
    protected $fillable = [
        'planting_id',
        'rice_growth_stage_id',
        'started_at',
        'completed_at',
        'status',
        'notes',
        'stage_data',
        'completion_percentage',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'stage_data' => 'json',
        'completion_percentage' => 'decimal:2',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DELAYED = 'delayed';
    const STATUS_SKIPPED = 'skipped';

    /**
     * Get the planting this stage belongs to
     */
    public function planting()
    {
        return $this->belongsTo(Planting::class);
    }

    /**
     * Get the rice growth stage
     */
    public function riceGrowthStage()
    {
        return $this->belongsTo(RiceGrowthStage::class);
    }

    /**
     * Start this stage
     */
    public function start()
    {
        $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'started_at' => Carbon::now(),
        ]);
    }

    /**
     * Complete this stage
     */
    public function complete($notes = null)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => Carbon::now(),
            'completion_percentage' => 100,
            'notes' => $notes,
        ]);
    }

    /**
     * Mark as delayed
     */
    public function markDelayed($notes = null)
    {
        $this->update([
            'status' => self::STATUS_DELAYED,
            'notes' => $notes,
        ]);
    }

    /**
     * Check if stage is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->started_at || $this->status === self::STATUS_COMPLETED) {
            return false;
        }

        $expectedEndDate = $this->started_at->addDays($this->riceGrowthStage->typical_duration_days);
        return Carbon::now()->gt($expectedEndDate);
    }

    /**
     * Get expected completion date
     */
    public function getExpectedCompletionDate()
    {
        if (!$this->started_at) return null;
        return $this->started_at->addDays($this->riceGrowthStage->typical_duration_days);
    }

    /**
     * Get duration in days
     */
    public function getDurationInDays()
    {
        if (!$this->started_at) return 0;
        
        $endDate = $this->completed_at ?? Carbon::now();
        return $this->started_at->diffInDays($endDate);
    }
}