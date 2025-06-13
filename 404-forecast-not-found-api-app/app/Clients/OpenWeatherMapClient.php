<?php

namespace App\Clients;

use App\Services\OpenWeatherMapService;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenWeatherMapClient extends BaseProviderClient
{
    protected string $baseUrl;
    protected string $apiKey;



    public function __construct()
    {
        $config = config('services.openweathermap');
        $this->baseUrl = $config['base_url'];
        $this->apiKey = $config['api_key'];
        $this->providerCode = 'openweathermap';
    }

    /**
     * @throws Exception
     */
    public function getCurrentWeather(float $lat, float $lon, string $exclude = ''): array
    {
        $response = $this->get("/weather", [
            'lat'     => $lat,
            'lon'     => $lon,
            'exclude' => $exclude,
            'appid'   => $this->apiKey,
        ]);

        if ($response->failed()) {
            throw new Exception("Failed to fetch weather data: " . $response->body());
        }

        return $response->json();
    }

    public function getHistoricalWeather(float $lat, float $lon, string $timestamp): array
    {
        $param = [
            'lat'     => $lat,
            'lon'     => $lon,
            'dt' => $timestamp,
            'appid'   => $this->apiKey,
        ];
        $result = $this->baseUrl . '/onecall/timemachine?' . http_build_query($param);
        return [$result];
        $response = Http::get("{$this->baseUrl}/onecall/timemachine", [
            'lat'   => $lat,
            'lon'   => $lon,
            'dt'    => $timestamp,
            'appid' => $this->apiKey,
        ]);

        if ($response->failed()) {
            throw new Exception("Failed to fetch historical weather data: " . $response->body());
        }

        return $response->json();
    }

    /**
     * @throws Exception
     */
    public function getWeatherForecast(float $lat, float $lon): array
    {
        $response = $this->get("/forecast", [
            'lat'     => $lat,
            'lon'     => $lon,
            'appid'   => $this->apiKey,
        ]);

        if ($response->failed()) {
            throw new Exception("Failed to fetch weather data: " . $response->body());
        }

        return $response->json();
    }

    public function getOverview(float $lat, float $lon): array
    {
        $response = Http::get("{$this->baseUrl}/overview", [
            'lat'   => $lat,
            'lon'   => $lon,
            'appid' => $this->apiKey,
        ]);

        if ($response->failed()) {
            throw new Exception("Failed to fetch overview: " . $response->body());
        }

        return $response->json();
    }
}
