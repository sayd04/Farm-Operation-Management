<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskTemplate extends Model
{
    protected $fillable = [
        'rice_growth_stage_id',
        'name',
        'description',
        'task_type',
        'days_from_stage_start',
        'estimated_duration_hours',
        'priority',
        'weather_conditions',
        'required_materials',
        'instructions',
        'safety_notes',
        'is_mandatory',
        'is_weather_dependent',
        'is_active',
    ];

    protected $casts = [
        'weather_conditions' => 'json',
        'required_materials' => 'json',
        'instructions' => 'json',
        'safety_notes' => 'json',
        'is_mandatory' => 'boolean',
        'is_weather_dependent' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Priority constants
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';

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
     * Get the rice growth stage this template belongs to
     */
    public function riceGrowthStage()
    {
        return $this->belongsTo(RiceGrowthStage::class);
    }

    /**
     * Get automated tasks created from this template
     */
    public function automatedTasks()
    {
        return $this->hasMany(AutomatedTask::class);
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for mandatory templates
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope by task type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('task_type', $type);
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Check if weather conditions are suitable for this task
     */
    public function isWeatherSuitable(WeatherLog $weather): bool
    {
        if (!$this->is_weather_dependent || !$this->weather_conditions) {
            return true;
        }

        $conditions = $this->weather_conditions;

        // Check temperature range
        if (isset($conditions['temperature_min']) && $weather->temperature < $conditions['temperature_min']) {
            return false;
        }
        if (isset($conditions['temperature_max']) && $weather->temperature > $conditions['temperature_max']) {
            return false;
        }

        // Check humidity range
        if (isset($conditions['humidity_min']) && $weather->humidity < $conditions['humidity_min']) {
            return false;
        }
        if (isset($conditions['humidity_max']) && $weather->humidity > $conditions['humidity_max']) {
            return false;
        }

        // Check wind speed
        if (isset($conditions['max_wind_speed']) && $weather->wind_speed > $conditions['max_wind_speed']) {
            return false;
        }

        // Check weather conditions
        if (isset($conditions['avoid_conditions']) && in_array($weather->conditions, $conditions['avoid_conditions'])) {
            return false;
        }

        if (isset($conditions['required_conditions']) && !in_array($weather->conditions, $conditions['required_conditions'])) {
            return false;
        }

        return true;
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'green',
            self::PRIORITY_MEDIUM => 'yellow',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_CRITICAL => 'red',
            default => 'gray'
        };
    }

    /**
     * Get task type display name
     */
    public function getTaskTypeDisplayAttribute()
    {
        return match($this->task_type) {
            self::TYPE_WATERING => 'Water Management',
            self::TYPE_FERTILIZING => 'Fertilizer Application',
            self::TYPE_WEEDING => 'Weed Control',
            self::TYPE_PEST_CONTROL => 'Pest Control',
            self::TYPE_HARVESTING => 'Harvesting',
            self::TYPE_MAINTENANCE => 'Field Maintenance',
            default => 'General Task'
        };
    }

    /**
     * Get estimated duration in human readable format
     */
    public function getEstimatedDurationDisplayAttribute()
    {
        $hours = $this->estimated_duration_hours;
        if ($hours < 1) {
            return ($hours * 60) . ' minutes';
        } elseif ($hours == 1) {
            return '1 hour';
        } elseif ($hours < 8) {
            return $hours . ' hours';
        } else {
            $days = round($hours / 8, 1);
            return $days . ' days';
        }
    }
}