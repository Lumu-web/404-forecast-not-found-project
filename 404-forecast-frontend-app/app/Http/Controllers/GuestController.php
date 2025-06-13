<?php

namespace App\Http\Controllers;

use App\Services\ForecastService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function __construct(
        private readonly ForecastService $forecastService
    ) {}

    public function index(): View
    {
        $data = $this->forecastService->getGuestSampleData();
        ['current' => $currentWeatherData, 'forecast' => $forecastWeatherData] = $data + ['current' => [], 'forecast' => []];
        $isGuest = true;
        return view('guest', compact('currentWeatherData', 'forecastWeatherData', 'isGuest'));
    }
}
