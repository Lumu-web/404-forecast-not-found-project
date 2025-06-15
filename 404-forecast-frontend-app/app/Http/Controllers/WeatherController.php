<?php

namespace App\Http\Controllers;

use App\Services\ForecastService;
use App\Services\HourlyForecastService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(
        private readonly ForecastService $forecastService,
        private readonly HourlyForecastService $hourlyForecastService
    ) {}

    /**
     * Generic overview endpoint: current + forecast for optional lat/lon.
     */
    public function overview(Request $request): JsonResponse
    {
        $lat = (float) $request->query('lat', 0);
        $lon = (float) $request->query('lon', 0);

        $data = $this->forecastService->fetchWeatherOverviewData($lat, $lon);

        return response()->json([
            'current'  => $data['current']  ?? [],
            'forecast' => $data['forecast'] ?? [],
        ]);
    }

    /**
     * Bar chart data for current conditions.
     */
    public function current(): JsonResponse
    {
        $current = $this->forecastService->fetchCurrentWeatherBarChartData();
        return response()->json(['data' => $current]);
    }

    public function currentMoodBarChartData(): JsonResponse
    {
        $currentMoodBarData = $this->forecastService->fetchCurrentWeatherBarChartData();
        return response()->json(['data' => $currentMoodBarData]);
    }

    /**
     * Full 5-day/3-hour forecast payload.
     */
    public function forecast(): JsonResponse
    {
        $forecast = $this->forecastService->fetchWeatherForecastData();
        return response()->json(['data' => $forecast]);
    }

    /**
     * Autocomplete/search for city names.
     */
    public function locations(Request $request): JsonResponse
    {
        $city = $request->input('city');
        if (! $city) {
            return response()->json(['error' => 'City parameter is required'], 422);
        }

        $locations = $this->forecastService->fetchCityLocationsData($city);
        return response()->json(['data' => $locations]);
    }

    /**
     * On-map or chart update: return current + forecast for selected coords.
     */
    public function cityCharts(Request $request): JsonResponse
    {
        $lat = (float) $request->query('lat', 0);
        $lon = (float) $request->query('lon', 0);

        if (! $lat || ! $lon) {
            return response()->json(['error' => 'Lat and Lon are required'], 422);
        }

        $overview = $this->forecastService->fetchWeatherOverviewData($lat, $lon);
        return response()->json([
            'current'  => $overview['current'],
            'forecast' => $overview['forecast'],
        ]);
    }

    /**
     * Endpoint for next-24h feels-like temperatures.
     */
    public function feelsLikeNext24(Request $request): JsonResponse
    {
        $lat = (float) $request->query('lat', 0);
        $lon = (float) $request->query('lon', 0);

        $data = $this->hourlyForecastService->getFeelsLikeNext24($lat, $lon);
        return response()->json(['data' => $data]);
    }

    /**
     * Endpoint for next-24h precipitation probabilities.
     */
    public function precipitationProbNext24(Request $request): JsonResponse
    {
        $lat = (float) $request->query('lat', 0);
        $lon = (float) $request->query('lon', 0);

        $data = $this->hourlyForecastService->getPrecipitationProbNext24($lat, $lon);
        return response()->json(['data' => $data]);
    }
}
