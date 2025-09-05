<?php

namespace App\Http\Controllers\Weather;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\WeatherLog;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class WeatherController extends Controller
{
    private WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Get current weather for a field
     */
    public function getCurrentWeather(Request $request, Field $field): JsonResponse
    {
        // Check if user can access this field
        $user = $request->user();
        if (!$user->isAdmin() && $field->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!isset($field->location['lat']) || !isset($field->location['lon'])) {
            return response()->json([
                'message' => 'Field location coordinates are required'
            ], 400);
        }

        $weatherData = $this->weatherService->getCurrentWeather(
            $field->location['lat'],
            $field->location['lon']
        );

        if (!$weatherData) {
            return response()->json([
                'message' => 'Unable to fetch weather data'
            ], 500);
        }

        // Save weather data to database
        $this->weatherService->updateFieldWeather($field);

        return response()->json([
            'field' => $field,
            'weather' => $weatherData,
            'alerts' => $this->weatherService->getWeatherAlerts($field)
        ]);
    }

    /**
     * Get weather forecast for a field
     */
    public function getForecast(Request $request, Field $field): JsonResponse
    {
        // Check if user can access this field
        $user = $request->user();
        if (!$user->isAdmin() && $field->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!isset($field->location['lat']) || !isset($field->location['lon'])) {
            return response()->json([
                'message' => 'Field location coordinates are required'
            ], 400);
        }

        $days = $request->get('days', 5);
        if ($days > 5) $days = 5; // API limitation

        $forecastData = $this->weatherService->getForecast(
            $field->location['lat'],
            $field->location['lon'],
            $days
        );

        if (!$forecastData) {
            return response()->json([
                'message' => 'Unable to fetch forecast data'
            ], 500);
        }

        return response()->json([
            'field' => $field,
            'forecast' => $forecastData
        ]);
    }

    /**
     * Get weather history for a field
     */
    public function getHistory(Request $request, Field $field): JsonResponse
    {
        // Check if user can access this field
        $user = $request->user();
        if (!$user->isAdmin() && $field->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'days' => 'integer|min:1|max:365',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $days = $request->get('days', 30);
        $perPage = $request->get('per_page', 20);

        $weatherLogs = WeatherLog::where('field_id', $field->id)
            ->where('recorded_at', '>=', now()->subDays($days))
            ->orderBy('recorded_at', 'desc')
            ->paginate($perPage);

        $stats = $this->weatherService->getFieldWeatherStats($field, $days);

        return response()->json([
            'field' => $field,
            'weather_logs' => $weatherLogs,
            'stats' => $stats
        ]);
    }

    /**
     * Get weather alerts for a field
     */
    public function getAlerts(Request $request, Field $field): JsonResponse
    {
        // Check if user can access this field
        $user = $request->user();
        if (!$user->isAdmin() && $field->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $alerts = $this->weatherService->getWeatherAlerts($field);

        return response()->json([
            'field' => $field,
            'alerts' => $alerts
        ]);
    }

    /**
     * Update weather data for a field
     */
    public function updateWeather(Request $request, Field $field): JsonResponse
    {
        // Check if user can access this field
        $user = $request->user();
        if (!$user->isAdmin() && $field->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $success = $this->weatherService->updateFieldWeather($field);

        if (!$success) {
            return response()->json([
                'message' => 'Failed to update weather data'
            ], 500);
        }

        $latestWeather = $field->latestWeather;

        return response()->json([
            'message' => 'Weather data updated successfully',
            'field' => $field,
            'latest_weather' => $latestWeather,
            'alerts' => $this->weatherService->getWeatherAlerts($field)
        ]);
    }

    /**
     * Update weather data for all user's fields
     */
    public function updateAllWeather(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $updated = $this->weatherService->updateAllFieldsWeather();
        } else {
            $fields = Field::where('user_id', $user->id)->get();
            $updated = 0;

            foreach ($fields as $field) {
                if ($this->weatherService->updateFieldWeather($field)) {
                    $updated++;
                }
            }
        }

        return response()->json([
            'message' => "Weather data updated for {$updated} fields",
            'updated_count' => $updated
        ]);
    }

    /**
     * Get weather dashboard data
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Field::query();
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $fields = $query->with('latestWeather')->get();

        $dashboardData = [
            'total_fields' => $fields->count(),
            'fields_with_weather' => $fields->filter(fn($field) => $field->latestWeather)->count(),
            'weather_alerts' => [],
            'field_weather' => []
        ];

        foreach ($fields as $field) {
            if ($field->latestWeather) {
                $alerts = $this->weatherService->getWeatherAlerts($field);
                $dashboardData['weather_alerts'] = array_merge(
                    $dashboardData['weather_alerts'],
                    array_map(fn($alert) => array_merge($alert, ['field' => $field->name ?? "Field {$field->id}"]), $alerts)
                );

                $dashboardData['field_weather'][] = [
                    'field' => $field,
                    'weather' => $field->latestWeather,
                    'alerts_count' => count($alerts)
                ];
            }
        }

        return response()->json($dashboardData);
    }
}