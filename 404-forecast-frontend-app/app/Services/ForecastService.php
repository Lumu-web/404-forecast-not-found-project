<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ForecastService
{
    public function __construct(
        protected string $weatherApiBaseUrl = ''
    ) {
        $this->weatherApiBaseUrl = config('services.forecast_api.v1.weather');
    }

    public function fetchCurrentWeatherData(float $lat = 0, float $lon = 0): array
    {
        $response = Http::forecastTokenApiCall()->get($this->weatherApiBaseUrl . '/current', [
            'lat' => $lat,
            'lon' => $lon,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    public function fetchWeatherForecastData(float $lat = 0, float $lon = 0): array
    {
        $response = Http::forecastTokenApiCall()->get($this->weatherApiBaseUrl . '/forecast', [
            'lat' => $lat,
            'lon' => $lon,
        ]);

        if ($response->successful()) {
            return $response->json();
        } elseif ($response->status() === 401) {
            // Token invalid or expired
            abort(401, 'Unauthorized to forecast API');
        } else {
            // Other error
            \Log::error('Forecast API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            abort(502, 'Forecast API error');
        }
    }

    public function fetchCityLocationsData(string $city): array
    {
        $response = Http::get($this->weatherApiBaseUrl . '/locations', [
            'city' => $city,
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            return $response->json(['error' => 'Failed to fetch data'], $response->status());
        }
    }

    public function fetchWeatherOverviewData(float $lat = null, float $lon = null)
    {
        $response = Http::forecastTokenApiCall()->get($this->weatherApiBaseUrl . '/overview', [
            'lat' => $lat,
            'lon' => $lon,
        ]);
        if ($response->successful()) {
            return $response->json();
        }

        if ($response->status() === 401) {
            abort(401, 'Unauthorized');
        }

        abort(500, 'Error fetching weather data');
    }

    public function getGuestSampleData()
    {
        try {
            $response = Http::get($this->weatherApiBaseUrl . '/guest-overview');
            return $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            dd('Error fetching guest sample data', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }

    }
}
