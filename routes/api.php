<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Weather\WeatherController;
use App\Http\Controllers\Farm\FieldController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Weather
    Route::prefix('weather')->group(function () {
        Route::get('/dashboard', [WeatherController::class, 'dashboard']);
        Route::post('/update-all', [WeatherController::class, 'updateAllWeather']);
        Route::prefix('fields/{field}')->group(function () {
            Route::get('/current', [WeatherController::class, 'getCurrentWeather']);
            Route::get('/forecast', [WeatherController::class, 'getForecast']);
            Route::get('/history', [WeatherController::class, 'getHistory']);
            Route::get('/alerts', [WeatherController::class, 'getAlerts']);
            Route::post('/update', [WeatherController::class, 'updateWeather']);
        });
    });

    // Fields CRUD
    Route::prefix('fields')->group(function () {
        Route::get('/', [FieldController::class, 'index']);
        Route::post('/', [FieldController::class, 'store']);
        Route::get('/{field}', [FieldController::class, 'show']);
        Route::put('/{field}', [FieldController::class, 'update']);
        Route::delete('/{field}', [FieldController::class, 'destroy']);
    });
});