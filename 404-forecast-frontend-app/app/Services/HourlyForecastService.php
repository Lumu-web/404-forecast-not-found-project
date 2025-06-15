<?php

namespace App\Services;

use App\Clients\ForecastClient;
use App\DTOs\ForecastTrendDto;
use App\DTOs\HourlyFeelsDto;
use App\DTOs\HourlyPopDto;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HourlyForecastService
{
    public function __construct(protected ForecastClient $client) {}

    /**
     * Get next 24 hours of feels-like temperatures
     * @return ForecastTrendDto[]
     */
    public function getFeelsLikeNext24(float $lat = 0, float $lon = 0, ?int $city = null): array
    {
        $cacheKey = "weather:hourly:feels:city:{$city}";
        $ttl = Carbon::now()->diffInSeconds(Carbon::now()->startOfHour()->addHour());

        return Cache::remember($cacheKey, $ttl, function() use ($lat, $lon) {
            $raw = $this->fetchWeatherForecastData($lat, $lon);

            $starting = $this->getFromCurrentHour($raw);

            return $starting
                ->map(fn($h) => HourlyFeelsDto::fromArray([
                    'forecast_at' => Carbon::parse($h['forecast_at'])->format('H:i'),
                    'feels_like'  => $h['feels_like'],
                ]))
                ->toArray();
        });
    }

    /**
     * Get next 24 hours of precipitation probability
     * @return HourlyPopDto[]
     */
    public function getPrecipitationProbNext24(float $lat, float $lon, ?int $city = null): array
    {
        $cacheKey = "weather:hourly:pop:city:{$city}";
        $ttl = Carbon::now()->diffInSeconds(Carbon::now()->startOfHour()->addHour());

        return Cache::remember($cacheKey, $ttl, function() use ($lat, $lon) {
            $raw = $this->fetchWeatherForecastData($lat, $lon);

            $starting = $this->getFromCurrentHour($raw);

            return $starting
                ->map(fn($h) => HourlyPopDto::fromArray([
                    'forecast_at' => Carbon::parse($h['forecast_at'])->format('H:i'),
                    'pop'         => $h['pop'],
                ]))
                ->toArray();
        });
    }

    /**
     * Fetch raw 5-day/3-hour forecast from the client (cached for 30 min)
     */
    public function fetchWeatherForecastData(float $lat = 0, float $lon = 0, ?int $city = null): array
    {
        $key = "weather:forecast:city:{$city}";

        try {
            return Cache::remember($key, 1800, fn() =>
            $this->client->getForecast($lat, $lon)
            );
        } catch (RequestException $e) {
            if ($e->response?->status() === 401) {
                abort(401, $e->getMessage() ?? 'Unauthorized to forecast API');
            }
            Log::error('Forecast API error', [
                'message' => $e->getMessage(),
                'status'  => $e->response?->status(),
            ]);
            abort(502, 'Forecast API error');
        }
    }

    private function getFromCurrentHour(array $raw): Collection
    {
        // find start of current hour
        $starting = collect($raw)
            ->skipUntil(fn($h) => Carbon::parse($h['forecast_at'])->hour === Carbon::now()->hour)
            ->values();

        return $starting->take(24);
    }
}
