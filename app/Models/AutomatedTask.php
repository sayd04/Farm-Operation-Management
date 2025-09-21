<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AutomatedTask extends Model
{
    protected $fillable = [
        'planting_id',
        'planting_stage_id',
        'task_template_id',
        'task_id',
        'title',
        'description',
        'scheduled_date',
        'due_date',
        'status',
        'weather_requirements',
        'generated_instructions',
        'delay_reason',
        'completed_at',
        'auto_generated',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'due_date' => 'datetime',
        'weather_requirements' => 'json',
        'generated_instructions' => 'json',
        'completed_at' => 'datetime',
        'auto_generated' => 'boolean',
    ];

    /**
     * Status constants
     */
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_READY = 'ready';
    const STATUS_WEATHER_DELAYED = 'weather_delayed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_SKIPPED = 'skipped';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the planting this task belongs to
     */
    public function planting()
    {
        return $this->belongsTo(Planting::class);
    }

    /**
     * Get the planting stage this task is associated with
     */
    public function plantingStage()
    {
        return $this->belongsTo(PlantingStage::class);
    }

    /**
     * Get the template this task was created from
     */
    public function taskTemplate()
    {
        return $this->belongsTo(TaskTemplate::class);
    }

    /**
     * Get the actual task if one was generated
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Scope for scheduled tasks
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * Scope for ready tasks
     */
    public function scopeReady($query)
    {
        return $query->where('status', self::STATUS_READY);
    }

    /**
     * Scope for overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now())
                    ->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_READY, self::STATUS_WEATHER_DELAYED]);
    }

    /**
     * Scope for today's tasks
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', Carbon::today());
    }

    /**
     * Scope for this week's tasks
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('scheduled_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Mark task as ready
     */
    public function markReady()
    {
        $this->update(['status' => self::STATUS_READY]);
    }

    /**
     * Mark task as weather delayed
     */
    public function markWeatherDelayed($reason = null)
    {
        $this->update([
            'status' => self::STATUS_WEATHER_DELAYED,
            'delay_reason' => $reason
        ]);
    }

    /**
     * Mark task as completed
     */
    public function markCompleted()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => Carbon::now()
        ]);
    }

    /**
     * Skip task
     */
    public function skip($reason = null)
    {
        $this->update([
            'status' => self::STATUS_SKIPPED,
            'delay_reason' => $reason
        ]);
    }

    /**
     * Cancel task
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'delay_reason' => $reason
        ]);
    }

    /**
     * Generate actual task from this automated task
     */
    public function generateTask($laborerId = null): Task
    {
        $task = Task::create([
            'planting_id' => $this->planting_id,
            'task_type' => $this->taskTemplate->task_type,
            'due_date' => $this->due_date,
            'description' => $this->description,
            'status' => Task::STATUS_PENDING,
            'assigned_to' => $laborerId,
        ]);

        $this->update(['task_id' => $task->id]);

        return $task;
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return Carbon::now()->gt($this->due_date) && 
               !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED, self::STATUS_SKIPPED]);
    }

    /**
     * Get days until due
     */
    public function getDaysUntilDue(): int
    {
        return Carbon::now()->diffInDays($this->due_date, false);
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_READY => 'Ready',
            self::STATUS_WEATHER_DELAYED => 'Weather Delayed',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_SKIPPED => 'Skipped',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_SCHEDULED => 'blue',
            self::STATUS_READY => 'green',
            self::STATUS_WEATHER_DELAYED => 'yellow',
            self::STATUS_COMPLETED => 'gray',
            self::STATUS_SKIPPED => 'orange',
            self::STATUS_CANCELLED => 'red',
            default => 'gray'
        };
    }

    /**
     * Check if weather conditions are suitable for this task
     */
    public function isWeatherSuitable(WeatherLog $weather): bool
    {
        return $this->taskTemplate->isWeatherSuitable($weather);
    }

    /**
     * Reschedule task
     */
    public function reschedule(Carbon $newDate, $reason = null)
    {
        $this->update([
            'scheduled_date' => $newDate,
            'due_date' => $newDate->copy()->addDays(1), // Give 1 day buffer
            'status' => self::STATUS_SCHEDULED,
            'delay_reason' => $reason
        ]);
    }
}