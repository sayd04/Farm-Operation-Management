<?php

namespace App\Services;

use App\Models\WeatherLog;
use App\Models\WeatherAlert;
use App\Models\Field;
use App\Models\Planting;
use App\Models\RiceGrowthStage;
use Carbon\Carbon;

class WeatherAnalyticsService
{
    /**
     * Analyze weather conditions for a field and generate alerts
     */
    public function analyzeFieldWeather(Field $field): array
    {
        $alerts = [];
        $latestWeather = $field->weatherLogs()->latest('recorded_at')->first();
        
        if (!$latestWeather) {
            return $alerts;
        }

        $recentWeather = $field->weatherLogs()
            ->where('recorded_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('recorded_at', 'desc')
            ->get();

        // Analyze for each active planting in this field
        $activePlantings = $field->plantings()
            ->whereIn('status', ['planted', 'growing', 'ready'])
            ->with('riceVariety', 'plantingStages.riceGrowthStage')
            ->get();

        foreach ($activePlantings as $planting) {
            $alerts = array_merge($alerts, $this->analyzePlantingWeather($planting, $latestWeather, $recentWeather));
        }

        // General field weather alerts
        $alerts = array_merge($alerts, $this->analyzeGeneralWeather($field, $latestWeather, $recentWeather));

        return $alerts;
    }

    /**
     * Analyze weather for specific planting
     */
    protected function analyzePlantingWeather(Planting $planting, WeatherLog $currentWeather, $recentWeather): array
    {
        $alerts = [];
        $currentStage = $planting->getCurrentStage();
        
        if (!$currentStage) {
            return $alerts;
        }

        $stageRequirements = $currentStage->riceGrowthStage->weather_requirements ?? [];
        
        // Temperature analysis
        $tempAlerts = $this->analyzeTemperature($planting, $currentWeather, $stageRequirements);
        $alerts = array_merge($alerts, $tempAlerts);

        // Humidity analysis
        $humidityAlerts = $this->analyzeHumidity($planting, $currentWeather, $stageRequirements);
        $alerts = array_merge($alerts, $humidityAlerts);

        // Rainfall analysis
        $rainfallAlerts = $this->analyzeRainfall($planting, $recentWeather, $stageRequirements);
        $alerts = array_merge($alerts, $rainfallAlerts);

        // Growth stage specific analysis
        $stageAlerts = $this->analyzeGrowthStageConditions($planting, $currentWeather, $currentStage);
        $alerts = array_merge($alerts, $stageAlerts);

        return $alerts;
    }

    /**
     * Analyze temperature conditions
     */
    protected function analyzeTemperature(Planting $planting, WeatherLog $weather, array $requirements): array
    {
        $alerts = [];
        $temp = $weather->temperature;
        
        // Critical temperature thresholds for rice
        if ($temp > 35) {
            $alerts[] = [
                'field_id' => $planting->field_id,
                'planting_id' => $planting->id,
                'alert_type' => WeatherAlert::TYPE_EXTREME_HEAT,
                'severity' => $temp > 38 ? WeatherAlert::SEVERITY_CRITICAL : WeatherAlert::SEVERITY_HIGH,
                'title' => 'Extreme Heat Alert',
                'description' => "Temperature is {$temp}°C. Rice plants may experience heat stress, affecting grain filling and yield.",
                'weather_data' => [
                    'current_temperature' => $temp,
                    'threshold' => 35,
                    'growth_stage' => $planting->getCurrentStage()?->riceGrowthStage?->name
                ],
                'recommendations' => [
                    'Increase water levels to 7-10cm to cool the field',
                    'Apply water during early morning and late afternoon',
                    'Monitor for signs of heat stress (leaf rolling, yellowing)',
                    'Consider foliar application of potassium to improve heat tolerance'
                ],
                'alert_date' => Carbon::now(),
                'expires_at' => Carbon::now()->addHours(12),
            ];
        }

        if ($temp < 18) {
            $alerts[] = [
                'field_id' => $planting->field_id,
                'planting_id' => $planting->id,
                'alert_type' => WeatherAlert::TYPE_COLD_STRESS,
                'severity' => $temp < 15 ? WeatherAlert::SEVERITY_HIGH : WeatherAlert::SEVERITY_MEDIUM,
                'title' => 'Cold Stress Warning',
                'description' => "Temperature is {$temp}°C. Cold stress may slow rice growth and affect flowering.",
                'weather_data' => [
                    'current_temperature' => $temp,
                    'threshold' => 18,
                    'growth_stage' => $planting->getCurrentStage()?->riceGrowthStage?->name
                ],
                'recommendations' => [
                    'Maintain shallow water depth (2-3cm) to allow soil warming',
                    'Avoid fertilizer application during cold periods',
                    'Monitor for delayed growth and adjust schedules accordingly'
                ],
                'alert_date' => Carbon::now(),
                'expires_at' => Carbon::now()->addHours(24),
            ];
        }

        return $alerts;
    }

    /**
     * Analyze humidity conditions
     */
    protected function analyzeHumidity(Planting $planting, WeatherLog $weather, array $requirements): array
    {
        $alerts = [];
        $humidity = $weather->humidity;
        
        if ($humidity > 90) {
            $alerts[] = [
                'field_id' => $planting->field_id,
                'planting_id' => $planting->id,
                'alert_type' => WeatherAlert::TYPE_HIGH_HUMIDITY,
                'severity' => WeatherAlert::SEVERITY_MEDIUM,
                'title' => 'High Humidity Alert',
                'description' => "Humidity is {$humidity}%. High humidity increases risk of fungal diseases.",
                'weather_data' => [
                    'current_humidity' => $humidity,
                    'threshold' => 90
                ],
                'recommendations' => [
                    'Improve field ventilation by maintaining proper plant spacing',
                    'Monitor for signs of blast, sheath blight, and bacterial leaf blight',
                    'Consider preventive fungicide application if conditions persist',
                    'Ensure proper drainage to reduce field humidity'
                ],
                'alert_date' => Carbon::now(),
                'expires_at' => Carbon::now()->addHours(8),
            ];
        }

        return $alerts;
    }

    /**
     * Analyze rainfall patterns
     */
    protected function analyzeRainfall(Planting $planting, $recentWeather, array $requirements): array
    {
        $alerts = [];
        $rainyDays = $recentWeather->where('conditions', 'rainy')->count();
        $stormyDays = $recentWeather->where('conditions', 'stormy')->count();
        
        // Check for prolonged wet conditions
        if ($rainyDays >= 5) {
            $alerts[] = [
                'field_id' => $planting->field_id,
                'planting_id' => $planting->id,
                'alert_type' => WeatherAlert::TYPE_FLOOD,
                'severity' => $rainyDays >= 7 ? WeatherAlert::SEVERITY_HIGH : WeatherAlert::SEVERITY_MEDIUM,
                'title' => 'Prolonged Wet Conditions',
                'description' => "Continuous rainfall for {$rainyDays} days may cause waterlogging and increase disease pressure.",
                'weather_data' => [
                    'rainy_days' => $rainyDays,
                    'stormy_days' => $stormyDays
                ],
                'recommendations' => [
                    'Ensure proper drainage to prevent waterlogging',
                    'Monitor for signs of root rot and other water-related diseases',
                    'Delay fertilizer application until conditions improve',
                    'Check and maintain bunds to prevent overflow'
                ],
                'alert_date' => Carbon::now(),
                'expires_at' => Carbon::now()->addDays(1),
            ];
        }

        // Check for drought conditions
        $clearDays = $recentWeather->where('conditions', 'clear')->count();
        if ($clearDays >= 7 && $rainyDays == 0) {
            $alerts[] = [
                'field_id' => $planting->field_id,
                'planting_id' => $planting->id,
                'alert_type' => WeatherAlert::TYPE_DROUGHT,
                'severity' => $clearDays >= 10 ? WeatherAlert::SEVERITY_HIGH : WeatherAlert::SEVERITY_MEDIUM,
                'title' => 'Drought Conditions',
                'description' => "No rainfall for {$clearDays} days. Water stress may affect rice growth and yield.",
                'weather_data' => [
                    'clear_days' => $clearDays,
                    'last_rain' => $clearDays
                ],
                'recommendations' => [
                    'Increase irrigation frequency and duration',
                    'Maintain consistent water levels (5-7cm)',
                    'Monitor soil moisture and plant stress indicators',
                    'Consider water-saving techniques like alternate wetting and drying'
                ],
                'alert_date' => Carbon::now(),
                'expires_at' => Carbon::now()->addDays(2),
            ];
        }

        return $alerts;
    }

    /**
     * Analyze growth stage specific conditions
     */
    protected function analyzeGrowthStageConditions(Planting $planting, WeatherLog $weather, $currentStage): array
    {
        $alerts = [];
        $stageName = $currentStage->riceGrowthStage->stage_code;
        
        // Flowering stage is most critical for weather
        if ($stageName === 'FLOWERING') {
            if ($weather->temperature > 33 || $weather->temperature < 20) {
                $alerts[] = [
                    'field_id' => $planting->field_id,
                    'planting_id' => $planting->id,
                    'alert_type' => WeatherAlert::TYPE_EXTREME_HEAT,
                    'severity' => WeatherAlert::SEVERITY_CRITICAL,
                    'title' => 'Critical Flowering Stage Weather',
                    'description' => 'Temperature extremes during flowering can cause spikelet sterility and reduce grain set.',
                    'weather_data' => [
                        'temperature' => $weather->temperature,
                        'growth_stage' => 'Flowering',
                        'critical_period' => true
                    ],
                    'recommendations' => [
                        'Maintain optimal water levels (3-5cm)',
                        'Provide maximum water management attention',
                        'Monitor for signs of poor pollination',
                        'Avoid any field disturbance during flowering'
                    ],
                    'alert_date' => Carbon::now(),
                    'expires_at' => Carbon::now()->addHours(6),
                ];
            }
        }

        // Tillering stage needs specific conditions
        if ($stageName === 'TILLERING' && $weather->wind_speed > 15) {
            $alerts[] = [
                'field_id' => $planting->field_id,
                'planting_id' => $planting->id,
                'alert_type' => WeatherAlert::TYPE_STRONG_WIND,
                'severity' => WeatherAlert::SEVERITY_MEDIUM,
                'title' => 'Strong Wind During Tillering',
                'description' => 'Strong winds may damage young tillers and affect plant establishment.',
                'weather_data' => [
                    'wind_speed' => $weather->wind_speed,
                    'growth_stage' => 'Tillering'
                ],
                'recommendations' => [
                    'Increase water depth to 5-7cm for plant stability',
                    'Check for lodging or plant damage after wind subsides',
                    'Consider windbreak installation for future seasons'
                ],
                'alert_date' => Carbon::now(),
                'expires_at' => Carbon::now()->addHours(12),
            ];
        }

        return $alerts;
    }

    /**
     * Analyze general weather conditions
     */
    protected function analyzeGeneralWeather(Field $field, WeatherLog $weather, $recentWeather): array
    {
        $alerts = [];

        // Pest risk analysis based on weather
        if ($weather->temperature >= 26 && $weather->temperature <= 30 && $weather->humidity >= 80) {
            $alerts[] = [
                'field_id' => $field->id,
                'planting_id' => null,
                'alert_type' => WeatherAlert::TYPE_PEST_RISK,
                'severity' => WeatherAlert::SEVERITY_MEDIUM,
                'title' => 'Brown Planthopper Risk',
                'description' => 'Current weather conditions (26-30°C, >80% humidity) favor brown planthopper development.',
                'weather_data' => [
                    'temperature' => $weather->temperature,
                    'humidity' => $weather->humidity,
                    'conditions' => 'Favorable for BPH'
                ],
                'recommendations' => [
                    'Increase field monitoring for brown planthopper',
                    'Check for hopper burn symptoms on rice plants',
                    'Prepare for possible insecticide application',
                    'Monitor neighboring fields for pest migration'
                ],
                'alert_date' => Carbon::now(),
                'expires_at' => Carbon::now()->addDays(3),
            ];
        }

        // Disease risk analysis
        if ($weather->humidity >= 85 && $weather->temperature >= 22 && $weather->temperature <= 28) {
            $alerts[] = [
                'field_id' => $field->id,
                'planting_id' => null,
                'alert_type' => WeatherAlert::TYPE_DISEASE_RISK,
                'severity' => WeatherAlert::SEVERITY_MEDIUM,
                'title' => 'Blast Disease Risk',
                'description' => 'Weather conditions favor blast disease development (22-28°C, >85% humidity).',
                'weather_data' => [
                    'temperature' => $weather->temperature,
                    'humidity' => $weather->humidity,
                    'risk_level' => 'High'
                ],
                'recommendations' => [
                    'Scout fields for early blast lesions on leaves',
                    'Ensure balanced nitrogen fertilization (avoid excess)',
                    'Improve field drainage and air circulation',
                    'Consider preventive fungicide application'
                ],
                'alert_date' => Carbon::now(),
                'expires_at' => Carbon::now()->addDays(5),
            ];
        }

        return $alerts;
    }

    /**
     * Generate weather recommendations for a planting
     */
    public function generateWeatherRecommendations(Planting $planting): array
    {
        $field = $planting->field;
        $latestWeather = $field->weatherLogs()->latest('recorded_at')->first();
        $currentStage = $planting->getCurrentStage();
        
        if (!$latestWeather || !$currentStage) {
            return [];
        }

        $recommendations = [];
        $stageName = $currentStage->riceGrowthStage->stage_code;
        $stageRequirements = $currentStage->riceGrowthStage->water_requirements ?? [];

        // Water management recommendations
        $waterRecs = $this->getWaterManagementRecommendations($stageName, $latestWeather, $stageRequirements);
        $recommendations['water_management'] = $waterRecs;

        // Fertilizer timing recommendations
        $fertilizerRecs = $this->getFertilizerRecommendations($stageName, $latestWeather);
        $recommendations['fertilizer'] = $fertilizerRecs;

        // Pest and disease management
        $pestRecs = $this->getPestDiseaseRecommendations($latestWeather);
        $recommendations['pest_disease'] = $pestRecs;

        return $recommendations;
    }

    /**
     * Get water management recommendations
     */
    protected function getWaterManagementRecommendations(string $stage, WeatherLog $weather, array $requirements): array
    {
        $recommendations = [];

        switch ($stage) {
            case 'LAND_PREP':
                $recommendations[] = 'Maintain 2-3cm water depth for field preparation';
                if ($weather->temperature > 30) {
                    $recommendations[] = 'Apply water during cooler hours to reduce evaporation';
                }
                break;

            case 'TRANSPLANT':
                $recommendations[] = 'Keep water level at 2-3cm for first week after transplanting';
                $recommendations[] = 'Ensure continuous water supply to prevent transplant shock';
                break;

            case 'TILLERING':
                if ($weather->temperature > 32) {
                    $recommendations[] = 'Increase water depth to 5-7cm to cool the field';
                } else {
                    $recommendations[] = 'Maintain 3-5cm water depth for optimal tillering';
                }
                break;

            case 'PANICLE_INIT':
            case 'FLOWERING':
                $recommendations[] = 'Critical water period - maintain 5-7cm depth consistently';
                $recommendations[] = 'Never allow field to dry during this stage';
                break;

            case 'GRAIN_FILL':
                $recommendations[] = 'Gradually reduce water to 2-3cm depth';
                break;

            case 'HARVEST':
                $recommendations[] = 'Drain field 1-2 weeks before harvest';
                break;
        }

        return $recommendations;
    }

    /**
     * Get fertilizer recommendations based on weather
     */
    protected function getFertilizerRecommendations(string $stage, WeatherLog $weather): array
    {
        $recommendations = [];

        if ($weather->conditions === 'rainy') {
            $recommendations[] = 'Delay fertilizer application until weather clears to prevent nutrient loss';
        } elseif ($weather->wind_speed > 10) {
            $recommendations[] = 'Apply fertilizer early morning or late afternoon when wind is calmer';
        } else {
            switch ($stage) {
                case 'TILLERING':
                    $recommendations[] = 'Good weather for nitrogen application to boost tillering';
                    break;
                case 'PANICLE_INIT':
                    $recommendations[] = 'Apply panicle fertilizer now - weather conditions are favorable';
                    break;
            }
        }

        return $recommendations;
    }

    /**
     * Get pest and disease management recommendations
     */
    protected function getPestDiseaseRecommendations(WeatherLog $weather): array
    {
        $recommendations = [];

        if ($weather->humidity > 85) {
            $recommendations[] = 'High humidity - monitor for fungal diseases like blast and sheath blight';
        }

        if ($weather->temperature >= 26 && $weather->temperature <= 30 && $weather->humidity >= 80) {
            $recommendations[] = 'Conditions favor brown planthopper - increase field scouting';
        }

        if ($weather->conditions === 'clear' && $weather->temperature > 30) {
            $recommendations[] = 'Hot, dry conditions may stress plants - monitor for increased pest activity';
        }

        return $recommendations;
    }

    /**
     * Create weather alerts for all active fields
     */
    public function generateAlertsForAllFields(): int
    {
        $alertsCreated = 0;
        $fields = Field::whereHas('plantings', function($query) {
            $query->whereIn('status', ['planted', 'growing', 'ready']);
        })->get();

        foreach ($fields as $field) {
            $alerts = $this->analyzeFieldWeather($field);
            
            foreach ($alerts as $alertData) {
                // Check if similar alert already exists
                $existingAlert = WeatherAlert::where('field_id', $alertData['field_id'])
                    ->where('alert_type', $alertData['alert_type'])
                    ->where('is_active', true)
                    ->where('created_at', '>=', Carbon::now()->subHours(6))
                    ->first();

                if (!$existingAlert) {
                    WeatherAlert::create($alertData);
                    $alertsCreated++;
                }
            }
        }

        return $alertsCreated;
    }
}