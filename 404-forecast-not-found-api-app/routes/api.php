<?php

use App\Http\Controllers\Api\V1\WeatherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

require __DIR__ . '/../routes/auth.php';

Route::prefix('v1')->group(function () {
    Route::prefix('weather')->group(function () {
        // Protected weather routes
        Route::middleware(['check.daily.openweather.api.usage', 'auth:sanctum'])->group(function () {
            Route::get('/current', [WeatherController::class, 'fetchCurrentWeather']);
            Route::get('/forecast', [WeatherController::class, 'getWeatherForecast']);
            Route::get('/history', [WeatherController::class, 'getHistoricalWeatherForecast']);
            Route::get('/overview', [WeatherController::class, 'getWeatherOverview']);
        });

        // Public or less-protected route
        Route::get('/locations', [WeatherController::class, 'getAutocompleteCity']);
        Route::get('/guest-overview', [WeatherController::class, 'getGuestWeatherOverview']);
    });
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('home');
});
