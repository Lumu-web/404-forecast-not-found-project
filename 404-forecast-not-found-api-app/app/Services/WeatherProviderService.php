<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

abstract class WeatherProviderService implements WeatherProviderInterface
{
    /**
     * Returns cached or live data (counts hits on cache miss).
     *
     * @param string $key Cache key
     * @param \Closure $callback Callback to fetch live data
     * @param int $minutes Cache duration in minutes
     * @return array Cached or fresh data
     */
    protected function getCachedData(string $key, \Closure $callback, int $minutes = 10): array
    {
        return Cache::remember($key, now()->addMinutes($minutes), function () use ($callback) {
            return $callback();
        });
    }

    /**
     * Fetch current weather data.
     *
     * @param float $lat
     * @param float $lon
     * @param string $city
     * @param string $country
     * @param string $exclude
     * @return array
     */
    abstract public function fetchCurrentWeather(float $lat, float $lon, string $city = '', string $country = '', string $exclude = ''): array;

    /**
     * Fetch weather forecast data.
     *
     * @param float $lat
     * @param float $lon
     * @param string $city
     * @param string $country
     * @return array
     */
    abstract public function fetchWeatherForecast(float $lat, float $lon, string $city = '', string $country = ''): array;

    /**
     * Fetch historical weather data for a specific date.
     *
     * @param float $lat
     * @param float $lon
     * @param string $date Date in 'Y-m-d' format
     * @return array
     */
    abstract public function fetchHistoricalWeatherForecast(float $lat, float $lon, string $date): array;

    /**
     * Fetch list of cities matching autocomplete query.
     *
     * @param string $query
     * @return array
     */
    abstract public function fetchAutoCompleteCityList(string $query): array;
}
