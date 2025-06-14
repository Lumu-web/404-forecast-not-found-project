<?php

namespace App\Services;

/**
 * Interface for weather data providers.
 */
interface WeatherProviderInterface
{
    public function fetchCurrentWeather(float $lat, float $lon, ?int $cityId = null, string $exclude = ''): array;
    public function fetchWeatherForecast(float $lat, float $lon, ?int $cityId): array;
    public function fetchAirQuality(float $lat, float $lon): array;
    public function fetchAutoCompleteCityList(string $query): array;
    public function fetchWeatherOverview(float $lat, float $lon, ?int $cityId = null): array;
    public function getDefaults(): array;
    public function getWeatherProviderCode(): string;
}
