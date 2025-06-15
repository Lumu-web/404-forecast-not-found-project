<?php

namespace App\Clients;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ForecastClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.forecast_api.v1.weather');
    }

    public function getCurrentWeather(float $lat, float $lon): Response
    {
        return Http::forecastTokenApiCall()
            ->get("{$this->baseUrl}/current", compact('lat', 'lon'));
    }

    public function getForecast(float $lat, float $lon): Response
    {
        return Http::forecastTokenApiCall()
            ->get("{$this->baseUrl}/forecast", compact('lat', 'lon'));
    }

    public function getCityLocations(string $city): Response
    {
        return Http::forecastTokenApiCall()
            ->get("{$this->baseUrl}/locations", compact('city'));
    }

    public function getOverview(?float $lat, ?float $lon): Response
    {
        return Http::forecastTokenApiCall()
            ->get("{$this->baseUrl}/overview", compact('lat', 'lon'));
    }

    public function getGuestCurrentSnapshotSampleData(): Response
    {
        return Http::get("{$this->baseUrl}/guest/current/snapshot/sample");
    }
}
