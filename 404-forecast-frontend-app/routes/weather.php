<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/current', [DashboardController::class, 'current'])->name('city.current');
        Route::get('/forecast', [DashboardController::class, 'forecast'])->name('city.forecast');
        Route::get('/locations', [DashboardController::class, 'locations'])->name('city.locations');
        Route::get('/cityCharts', [DashboardController::class, 'cityCharts'])->name('city.charts');
    });


