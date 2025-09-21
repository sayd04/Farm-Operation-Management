<?php

namespace App\Services;

use App\Models\TaskTemplate;
use App\Models\AutomatedTask;
use App\Models\Planting;
use App\Models\PlantingStage;
use App\Models\WeatherLog;
use App\Models\Task;
use Carbon\Carbon;

class TaskAutomationService
{
    protected WeatherAnalyticsService $weatherService;

    public function __construct(WeatherAnalyticsService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Generate automated tasks for a planting when a growth stage starts
     */
    public function generateTasksForStage(PlantingStage $plantingStage): array
    {
        $generatedTasks = [];
        $riceGrowthStage = $plantingStage->riceGrowthStage;
        $planting = $plantingStage->planting;

        // Get task templates for this growth stage
        $templates = TaskTemplate::where('rice_growth_stage_id', $riceGrowthStage->id)
                                ->active()
                                ->orderBy('days_from_stage_start')
                                ->get();

        foreach ($templates as $template) {
            $automatedTask = $this->createAutomatedTask($planting, $plantingStage, $template);
            $generatedTasks[] = $automatedTask;
        }

        return $generatedTasks;
    }

    /**
     * Create an automated task from a template
     */
    protected function createAutomatedTask(Planting $planting, PlantingStage $plantingStage, TaskTemplate $template): AutomatedTask
    {
        $stageStartDate = $plantingStage->started_at ?: Carbon::now();
        $scheduledDate = $stageStartDate->copy()->addDays($template->days_from_stage_start);
        $dueDate = $scheduledDate->copy()->addDays(2); // Give 2 days to complete

        // Customize task based on planting and current conditions
        $customizedInstructions = $this->customizeInstructions($template, $planting);
        $customizedDescription = $this->customizeDescription($template, $planting);

        return AutomatedTask::create([
            'planting_id' => $planting->id,
            'planting_stage_id' => $plantingStage->id,
            'task_template_id' => $template->id,
            'title' => $template->name,
            'description' => $customizedDescription,
            'scheduled_date' => $scheduledDate,
            'due_date' => $dueDate,
            'status' => AutomatedTask::STATUS_SCHEDULED,
            'weather_requirements' => $template->weather_conditions,
            'generated_instructions' => $customizedInstructions,
        ]);
    }

    /**
     * Process all scheduled automated tasks
     */
    public function processScheduledTasks(): array
    {
        $results = [
            'ready' => 0,
            'delayed' => 0,
            'generated' => 0,
            'errors' => []
        ];

        // Get tasks scheduled for today or earlier that haven't been processed
        $scheduledTasks = AutomatedTask::where('scheduled_date', '<=', Carbon::now())
                                     ->where('status', AutomatedTask::STATUS_SCHEDULED)
                                     ->with(['planting.field', 'taskTemplate'])
                                     ->get();

        foreach ($scheduledTasks as $task) {
            try {
                $this->processAutomatedTask($task, $results);
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Process a single automated task
     */
    protected function processAutomatedTask(AutomatedTask $task, array &$results): void
    {
        $planting = $task->planting;
        $field = $planting->field;
        
        // Get latest weather for the field
        $latestWeather = $field->weatherLogs()->latest('recorded_at')->first();

        if (!$latestWeather) {
            // No weather data, mark as ready anyway
            $task->markReady();
            $results['ready']++;
            return;
        }

        // Check if weather conditions are suitable
        if ($task->taskTemplate->is_weather_dependent) {
            if (!$task->isWeatherSuitable($latestWeather)) {
                $delayReason = $this->getWeatherDelayReason($task->taskTemplate, $latestWeather);
                $task->markWeatherDelayed($delayReason);
                
                // Try to reschedule for better weather
                $newDate = $this->findNextSuitableWeatherDate($task);
                if ($newDate) {
                    $task->reschedule($newDate, $delayReason);
                }
                
                $results['delayed']++;
                return;
            }
        }

        // Weather is suitable or task is not weather dependent
        $task->markReady();
        $results['ready']++;

        // Auto-generate actual task if configured to do so
        if ($this->shouldAutoGenerateTask($task)) {
            $actualTask = $task->generateTask();
            $results['generated']++;
        }
    }

    /**
     * Get weather delay reason
     */
    protected function getWeatherDelayReason(TaskTemplate $template, WeatherLog $weather): string
    {
        $conditions = $template->weather_conditions;
        $reasons = [];

        if (isset($conditions['temperature_min']) && $weather->temperature < $conditions['temperature_min']) {
            $reasons[] = "Temperature too low ({$weather->temperature}°C, minimum {$conditions['temperature_min']}°C)";
        }
        if (isset($conditions['temperature_max']) && $weather->temperature > $conditions['temperature_max']) {
            $reasons[] = "Temperature too high ({$weather->temperature}°C, maximum {$conditions['temperature_max']}°C)";
        }
        if (isset($conditions['max_wind_speed']) && $weather->wind_speed > $conditions['max_wind_speed']) {
            $reasons[] = "Wind speed too high ({$weather->wind_speed} km/h, maximum {$conditions['max_wind_speed']} km/h)";
        }
        if (isset($conditions['avoid_conditions']) && in_array($weather->conditions, $conditions['avoid_conditions'])) {
            $reasons[] = "Unsuitable weather conditions ({$weather->conditions})";
        }

        return implode('; ', $reasons) ?: 'Weather conditions not suitable';
    }

    /**
     * Find next suitable weather date (simplified - would need weather forecast API)
     */
    protected function findNextSuitableWeatherDate(AutomatedTask $task): ?Carbon
    {
        // This would ideally use weather forecast API
        // For now, just reschedule 2 days later
        return $task->scheduled_date->copy()->addDays(2);
    }

    /**
     * Check if we should auto-generate actual task
     */
    protected function shouldAutoGenerateTask(AutomatedTask $task): bool
    {
        // For now, only auto-generate critical and high priority tasks
        return in_array($task->taskTemplate->priority, ['critical', 'high']);
    }

    /**
     * Customize instructions based on planting conditions
     */
    protected function customizeInstructions(TaskTemplate $template, Planting $planting): array
    {
        $instructions = $template->instructions ?: [];
        $field = $planting->field;
        $variety = $planting->riceVariety;

        // Add variety-specific instructions
        if ($template->task_type === 'fertilizing') {
            $instructions['variety_specific'] = $this->getFertilizerInstructions($variety);
        }

        // Add field-specific instructions
        if ($template->task_type === 'watering') {
            $instructions['field_specific'] = $this->getWateringInstructions($field);
        }

        // Add season-specific instructions
        $instructions['season_specific'] = $this->getSeasonSpecificInstructions($template, $planting->season);

        return $instructions;
    }

    /**
     * Customize description based on planting conditions
     */
    protected function customizeDescription(TaskTemplate $template, Planting $planting): string
    {
        $description = $template->description;
        $field = $planting->field;
        $variety = $planting->riceVariety;

        // Replace placeholders
        $description = str_replace('{field_name}', $field->name, $description);
        $description = str_replace('{variety_name}', $variety->name, $description);
        $description = str_replace('{area}', $planting->area_planted . ' hectares', $description);
        $description = str_replace('{season}', $planting->season, $description);

        return $description;
    }

    /**
     * Get fertilizer instructions for rice variety
     */
    protected function getFertilizerInstructions(RiceVariety $variety): array
    {
        $baseInstructions = [
            'Apply fertilizer evenly across the field',
            'Apply during dry weather for best absorption',
            'Water lightly after application'
        ];

        // Add variety-specific rates
        if (str_contains(strtolower($variety->name), 'ir64')) {
            $baseInstructions[] = 'Recommended rate: 90-60-30 kg/ha NPK for IR64';
        } else {
            $baseInstructions[] = 'Use standard NPK rates for this variety';
        }

        return $baseInstructions;
    }

    /**
     * Get watering instructions for field
     */
    protected function getWateringInstructions(Field $field): array
    {
        $instructions = [
            'Check current water level before irrigation',
            'Ensure even water distribution across the field'
        ];

        if ($field->drainage_quality === 'poor') {
            $instructions[] = 'Monitor for waterlogging due to poor drainage';
        }

        if ($field->water_access === 'excellent') {
            $instructions[] = 'Maintain optimal water levels consistently';
        } else {
            $instructions[] = 'Conserve water - use efficient irrigation methods';
        }

        return $instructions;
    }

    /**
     * Get season-specific instructions
     */
    protected function getSeasonSpecificInstructions(TaskTemplate $template, string $season): array
    {
        $instructions = [];

        if ($season === 'wet') {
            switch ($template->task_type) {
                case 'pest_control':
                    $instructions[] = 'Wet season increases pest pressure - monitor closely';
                    break;
                case 'fertilizing':
                    $instructions[] = 'Risk of nutrient leaching in wet season - apply in split doses';
                    break;
                case 'weeding':
                    $instructions[] = 'Weeds grow faster in wet season - increase frequency';
                    break;
            }
        } else { // dry season
            switch ($template->task_type) {
                case 'watering':
                    $instructions[] = 'Dry season requires more frequent irrigation';
                    break;
                case 'fertilizing':
                    $instructions[] = 'Better fertilizer retention in dry season';
                    break;
            }
        }

        return $instructions;
    }

    /**
     * Get upcoming tasks for a planting
     */
    public function getUpcomingTasks(Planting $planting, int $days = 7): \Illuminate\Database\Eloquent\Collection
    {
        return AutomatedTask::where('planting_id', $planting->id)
                          ->whereBetween('scheduled_date', [
                              Carbon::now(),
                              Carbon::now()->addDays($days)
                          ])
                          ->whereIn('status', [
                              AutomatedTask::STATUS_SCHEDULED,
                              AutomatedTask::STATUS_READY,
                              AutomatedTask::STATUS_WEATHER_DELAYED
                          ])
                          ->with(['taskTemplate', 'plantingStage'])
                          ->orderBy('scheduled_date')
                          ->get();
    }

    /**
     * Get overdue tasks for a farmer
     */
    public function getOverdueTasks(User $farmer): \Illuminate\Database\Eloquent\Collection
    {
        return AutomatedTask::whereHas('planting.field', function($query) use ($farmer) {
                              $query->where('user_id', $farmer->id);
                          })
                          ->overdue()
                          ->with(['planting.field', 'taskTemplate'])
                          ->orderBy('due_date')
                          ->get();
    }

    /**
     * Get task statistics for a farmer
     */
    public function getTaskStats(User $farmer): array
    {
        $baseQuery = AutomatedTask::whereHas('planting.field', function($query) use ($farmer) {
            $query->where('user_id', $farmer->id);
        });

        return [
            'total_tasks' => $baseQuery->count(),
            'scheduled' => $baseQuery->where('status', AutomatedTask::STATUS_SCHEDULED)->count(),
            'ready' => $baseQuery->where('status', AutomatedTask::STATUS_READY)->count(),
            'weather_delayed' => $baseQuery->where('status', AutomatedTask::STATUS_WEATHER_DELAYED)->count(),
            'completed' => $baseQuery->where('status', AutomatedTask::STATUS_COMPLETED)->count(),
            'overdue' => $baseQuery->overdue()->count(),
            'this_week' => $baseQuery->thisWeek()->count(),
            'completion_rate' => $this->calculateCompletionRate($farmer),
        ];
    }

    /**
     * Calculate task completion rate for a farmer
     */
    protected function calculateCompletionRate(User $farmer): float
    {
        $totalTasks = AutomatedTask::whereHas('planting.field', function($query) use ($farmer) {
                                     $query->where('user_id', $farmer->id);
                                 })
                                 ->where('due_date', '<', Carbon::now())
                                 ->count();

        if ($totalTasks === 0) {
            return 100.0;
        }

        $completedTasks = AutomatedTask::whereHas('planting.field', function($query) use ($farmer) {
                                         $query->where('user_id', $farmer->id);
                                     })
                                     ->where('status', AutomatedTask::STATUS_COMPLETED)
                                     ->where('due_date', '<', Carbon::now())
                                     ->count();

        return ($completedTasks / $totalTasks) * 100;
    }

    /**
     * Initialize tasks for all active plantings
     */
    public function initializeTasksForAllPlantings(): int
    {
        $tasksCreated = 0;
        
        $activePlantings = Planting::whereIn('status', ['planted', 'growing', 'ready'])
                                  ->with(['plantingStages.riceGrowthStage'])
                                  ->get();

        foreach ($activePlantings as $planting) {
            foreach ($planting->plantingStages as $stage) {
                if ($stage->status === PlantingStage::STATUS_IN_PROGRESS) {
                    $tasks = $this->generateTasksForStage($stage);
                    $tasksCreated += count($tasks);
                }
            }
        }

        return $tasksCreated;
    }
}