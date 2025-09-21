<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tasks';

    protected $fillable = [
        'planting_id',
        'task_type',
        'due_date',
        'description',
        'status',
        'assigned_to',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Task type constants
     */
    const TYPE_WATERING = 'watering';
    const TYPE_FERTILIZING = 'fertilizing';
    const TYPE_WEEDING = 'weeding';
    const TYPE_PEST_CONTROL = 'pest_control';
    const TYPE_HARVESTING = 'harvesting';
    const TYPE_MAINTENANCE = 'maintenance';

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the planting that owns the task
     */
    public function planting()
    {
        return $this->belongsTo(Planting::class);
    }

    /**
     * Get the laborer assigned to this task
     */
    public function laborer()
    {
        return $this->belongsTo(Laborer::class, 'assigned_to');
    }

    /**
     * Get the labor wages for this task
     */
    public function laborWages()
    {
        return $this->hasMany(LaborWage::class);
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date < Carbon::now() && 
               $this->status !== self::STATUS_COMPLETED;
    }

    /**
     * Get days until due
     */
    public function daysUntilDue(): int
    {
        return Carbon::now()->diffInDays($this->due_date, false);
    }

    /**
     * Mark task as completed
     */
    public function markCompleted()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    /**
     * Get total labor cost for this task
     */
    public function getTotalLaborCostAttribute()
    {
        return $this->laborWages()->sum('wage_amount');
    }
}