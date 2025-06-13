<?php

namespace App\Services;

interface WeatherProviderInterface
{
    public function fetchCurrentWeather(float $lat, float $lon, string $city = '', string $country = '', string $exclude = ''): array;
    public function fetchWeatherForecast(float $lat, float $lon, string $city = '', string $country = ''): array;

    public function fetchWeatherOverview(float $lat, float $lon, string $city = '', string $country = ''): array;

    public function getDefaults(): array;

    public function getWeatherProviderCode(): string;
}
