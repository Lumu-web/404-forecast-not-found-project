<?php

namespace App\Http\Controllers;

use App\Services\CurrentWeatherService;
use App\Services\ForecastService;
use App\Services\HourlyForecastService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DashboardController extends Controller
{
    public function __construct(
        protected CurrentWeatherService $currentWeatherService,
        protected ForecastService $forecastService,
        protected HourlyForecastService $hourlyForecastService
    ) {}

    /**
     * Display the dashboard with all initial graph data.
     *
     * Loads current conditions, forecast overview,
     * plus the three main bar charts: current mood,
     * next-24h feels-like, and next-24h precipitation.
     */
    public function index(Request $request)
    {
        try {
            // 1. Default overview data (current + forecast)
            [$currentMoodChartData, $next24FeelsLikeData] = $this->getDefaultWeatherData();

            return view('dashboard.index', compact(
                'currentMoodChartData',
                'next24FeelsLikeData'
            ));

        } catch (HttpException $e) {
            if ($e->getStatusCode() === 401) {
                return redirect()->route('login')
                    ->withErrors('Please login to access weather data.');
            }
            return response()->view('errors.custom', ['message' => $e->getMessage()], $e->getStatusCode());

        } catch (\Throwable $e) {
            // Generic fallback
            return response()->view('errors.custom', ['message' => 'An unexpected error occurred.'], 500);
        }
    }

    /**
     * Helper to load default weather overview data.
     * Returns [currentData, forecastData].
     */
    private function getDefaultWeatherData(): array
    {
        $currentMoodChartData   = $this->currentWeatherService->fetchCurrentWeatherBarChartData();
        $next24FeelsLikeTrend   = $this->hourlyForecastService->getFeelsLikeNext24();

        return [
            'currentMoodChartData'   => $currentMoodChartData,
            'next24FeelsLikeTrend'   => $next24FeelsLikeTrend,
        ];
    }
}
