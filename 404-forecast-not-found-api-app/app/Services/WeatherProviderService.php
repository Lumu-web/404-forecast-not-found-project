<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

abstract class WeatherProviderService implements WeatherProviderInterface
{
    protected function cache(string $key, \Closure $callback, int $ttl = 600): mixed
    {
        return Cache::remember($key, $ttl, $callback);
    }

    abstract public function fetchCurrentWeather(float $lat, float $lon, ?int $cityId = null, string $exclude = ''): array;
    abstract public function fetchWeatherForecast(float $lat, float $lon, ?int $city): array;
    abstract public function fetchAirQuality(float $lat, float $lon): array;
    abstract public function fetchAutoCompleteCityList(string $query): array;
    abstract public function fetchWeatherOverview(float $lat, float $lon, string $city, string $country): array;
    abstract public function getDefaults(): array;
    abstract public function getWeatherProviderCode(): string;
}
