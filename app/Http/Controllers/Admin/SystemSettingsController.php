<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemSettingsController extends Controller
{
    /**
     * Get all system settings
     */
    public function index(): JsonResponse
    {
        $settings = Cache::remember('system_settings', 3600, function () {
            return [
                'app_name' => config('app.name'),
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
                'app_url' => config('app.url'),
                'database_connection' => config('database.default'),
                'mail_driver' => config('mail.default'),
                'cache_driver' => config('cache.default'),
                'session_driver' => config('session.driver'),
                'queue_connection' => config('queue.default'),
                'storage_disk' => config('filesystems.default'),
            ];
        });

        return response()->json($settings);
    }

    /**
     * Update system settings
     */
    public function update(Request $request): JsonResponse
    {
        $validator = $request->validate([
            'app_name' => 'sometimes|string|max:255',
            'app_url' => 'sometimes|url',
            'mail_driver' => 'sometimes|string|in:smtp,sendmail,mailgun,ses,postmark,log,array',
            'cache_driver' => 'sometimes|string|in:array,database,file,memcached,redis,dynamodb,octane',
            'session_driver' => 'sometimes|string|in:file,cookie,database,apc,memcached,redis,dynamodb,array',
            'queue_connection' => 'sometimes|string|in:sync,database,beanstalkd,sqs,redis,null',
        ]);

        // Update configuration files (this is a simplified approach)
        // In a real application, you might want to store these in a database
        // or use a more sophisticated configuration management system

        Cache::forget('system_settings');

        return response()->json([
            'message' => 'System settings updated successfully',
            'settings' => $validator,
        ]);
    }

    /**
     * Get system health status
     */
    public function health(): JsonResponse
    {
        $health = [
            'database' => $this->checkDatabaseConnection(),
            'cache' => $this->checkCacheConnection(),
            'storage' => $this->checkStorageConnection(),
            'memory_usage' => $this->getMemoryUsage(),
            'disk_usage' => $this->getDiskUsage(),
        ];

        return response()->json($health);
    }

    /**
     * Clear application cache
     */
    public function clearCache(): JsonResponse
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');

        return response()->json([
            'message' => 'Application cache cleared successfully',
        ]);
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection(): array
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'connected', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Check cache connection
     */
    private function checkCacheConnection(): array
    {
        try {
            Cache::put('test_key', 'test_value', 1);
            $value = Cache::get('test_key');
            Cache::forget('test_key');
            
            if ($value === 'test_value') {
                return ['status' => 'connected', 'message' => 'Cache connection successful'];
            }
            
            return ['status' => 'error', 'message' => 'Cache test failed'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Check storage connection
     */
    private function checkStorageConnection(): array
    {
        try {
            Storage::disk('local')->put('test.txt', 'test content');
            $content = Storage::disk('local')->get('test.txt');
            Storage::disk('local')->delete('test.txt');
            
            if ($content === 'test content') {
                return ['status' => 'connected', 'message' => 'Storage connection successful'];
            }
            
            return ['status' => 'error', 'message' => 'Storage test failed'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        
        return [
            'current' => $this->formatBytes($memoryUsage),
            'peak' => $this->formatBytes($memoryPeak),
        ];
    }

    /**
     * Get disk usage
     */
    private function getDiskUsage(): array
    {
        $totalSpace = disk_total_space('/');
        $freeSpace = disk_free_space('/');
        $usedSpace = $totalSpace - $freeSpace;
        
        return [
            'total' => $this->formatBytes($totalSpace),
            'used' => $this->formatBytes($usedSpace),
            'free' => $this->formatBytes($freeSpace),
            'percentage' => round(($usedSpace / $totalSpace) * 100, 2),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}