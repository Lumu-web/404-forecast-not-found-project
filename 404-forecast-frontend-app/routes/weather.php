<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/locations', [WeatherController::class, 'locations'])->name('city.locations');
        Route::get('/cityCharts', [WeatherController::class, 'cityCharts'])->name('city.charts');
    });

    Route::prefix('weather')->name('weather.')->group(function () {
        Route::get('/overview', [WeatherController::class, 'overview'])->name('overview');
        Route::get('/weather/current-mood', [WeatherController::class, 'currentMoodBarChartData'])
            ->name('weather.current.mood');
        Route::get('/weather/feels-like/next-24h', [WeatherController::class, 'feelsLikeNext24'])
            ->name('weather.feels_like.next_24h');

        Route::get('/locations', [WeatherController::class, 'locations'])->name('locations');
    });


