<?php

namespace App\Clients;

class OpenWeatherMapClient extends BaseProviderClient
{
    public function __construct()
    {
        $config = config('services.openweathermap');
        parent::__construct($config['base_url'], $config['api_key'], $config['daily_limit'] ?? 1000);
        $this->setProviderCode('openweathermap');
    }

    public function getCurrentWeather(float $lat, float $lon, string $exclude = ''): array
    {
        return $this->request('/weather', compact('lat', 'lon', 'exclude'));
    }

    public function getWeatherForecast(float $lat, float $lon): array
    {
        return $this->request('/forecast', compact('lat', 'lon'));
    }

    public function getAirQuality(float $lat, float $lon): array
    {
        return $this->request('/air_pollution', compact('lat', 'lon'));
    }
}

