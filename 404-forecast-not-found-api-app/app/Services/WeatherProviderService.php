<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

abstract class WeatherProviderService implements WeatherProviderInterface
{
    protected int $defaultTtl = 10; // minutes

    /**
     * Generic cache wrapper.
     *
     * @param string $prefix Stable prefix (e.g. 'current_weather')
     * @param array $params Inputs affecting cache key
     * @param Closure $callback API call returning array
     * @param int|null $minutes TTL in minutes
     * @return array
     */
    protected function cacheCall(string $prefix, array $params, Closure $callback, ?int $minutes = null): array
    {
        $ttl = $minutes ?? $this->defaultTtl;
        $key = $this->makeCacheKey($prefix, $params);

        return Cache::remember($key, now()->addMinutes($ttl), $callback);
    }

    /**
     * Build a deterministic cache-key from a prefix + parameters.
     *
     * @param string $prefix
     * @param array $params
     * @return string
     */
    protected function makeCacheKey(string $prefix, array $params): string
    {
        ksort($params);

        $payload = collect($params)
            ->map(fn($value, $key) => "{$key}:{$value}")
            ->implode('_');

        // if worries about length, wrap payload in md5(): md5($payload)
        return "{$prefix}_{$payload}";
    }

    abstract public function fetchCurrentWeather(float $lat, float $lon, ?int $cityId = null, string $exclude = ''): array;

    abstract public function fetchWeatherForecast(float $lat, float $lon, ?int $cityId = null): array;

    abstract public function fetchAirQuality(float $lat, float $lon): array;

    abstract public function fetchAutoCompleteCityList(string $query): array;

    abstract public function fetchGuestWeatherOverview(
        float $lat,
        float $lon,
        ?int  $cityId = null
    ): array;

    abstract public function getDefaults(): array;

    abstract public function getWeatherProviderCode(): string;
}
