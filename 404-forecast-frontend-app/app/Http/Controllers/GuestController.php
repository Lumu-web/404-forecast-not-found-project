<?php

namespace App\Http\Controllers;

use App\Services\CurrentWeatherService;
use App\Services\ForecastService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function __construct(
        private readonly CurrentWeatherService $currentWeatherService
    ) {}

    public function index(): View
    {
        $currentMoodBarData = $this->currentWeatherService->getGuestCurrentSnapShotSampleData();
        $isGuest = true;
        return view('guest.index', compact('currentMoodBarData', 'isGuest'));
    }
}
