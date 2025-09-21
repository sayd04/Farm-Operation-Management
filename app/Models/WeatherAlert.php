<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WeatherAlert extends Model
{
    protected $fillable = [
        'field_id',
        'planting_id',
        'alert_type',
        'severity',
        'title',
        'description',
        'weather_data',
        'recommendations',
        'alert_date',
        'expires_at',
        'is_active',
        'is_read',
    ];

    protected $casts = [
        'weather_data' => 'json',
        'recommendations' => 'json',
        'alert_date' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'is_read' => 'boolean',
    ];

    /**
     * Alert type constants
     */
    const TYPE_DROUGHT = 'drought';
    const TYPE_FLOOD = 'flood';
    const TYPE_EXTREME_HEAT = 'extreme_heat';
    const TYPE_COLD_STRESS = 'cold_stress';
    const TYPE_HIGH_HUMIDITY = 'high_humidity';
    const TYPE_STRONG_WIND = 'strong_wind';
    const TYPE_PEST_RISK = 'pest_risk';
    const TYPE_DISEASE_RISK = 'disease_risk';

    /**
     * Severity constants
     */
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Get the field this alert belongs to
     */
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the planting this alert is related to
     */
    public function planting()
    {
        return $this->belongsTo(Planting::class);
    }

    /**
     * Scope for active alerts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', Carbon::now());
                    });
    }

    /**
     * Scope for unread alerts
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Mark alert as read
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Deactivate alert
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Check if alert is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && Carbon::now()->gt($this->expires_at);
    }

    /**
     * Get severity color for UI
     */
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            self::SEVERITY_LOW => 'green',
            self::SEVERITY_MEDIUM => 'yellow',
            self::SEVERITY_HIGH => 'orange',
            self::SEVERITY_CRITICAL => 'red',
            default => 'gray'
        };
    }

    /**
     * Get alert icon
     */
    public function getAlertIconAttribute()
    {
        return match($this->alert_type) {
            self::TYPE_DROUGHT => 'sun',
            self::TYPE_FLOOD => 'cloud-rain',
            self::TYPE_EXTREME_HEAT => 'thermometer-hot',
            self::TYPE_COLD_STRESS => 'thermometer-cold',
            self::TYPE_HIGH_HUMIDITY => 'droplets',
            self::TYPE_STRONG_WIND => 'wind',
            self::TYPE_PEST_RISK => 'bug',
            self::TYPE_DISEASE_RISK => 'exclamation-triangle',
            default => 'info-circle'
        };
    }
}