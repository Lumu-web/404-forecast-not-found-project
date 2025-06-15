<?php

namespace App\Services;

use App\Clients\ForecastClient;
use App\DTOs\ForecastTrendDto;
use App\DTOs\DailyWindStatsDto;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DailyForecastService
{
    public function __construct(protected ForecastClient $client) {}

    /**
     * 7-day temperature trend (min/max or avg)
     * @return array<ForecastTrendDto>
     */
    public function get7DayTrend(float $lat, float $lon): array
    {
        $data = Cache::remember("weather:daily:trend:{$lat},{$lon}", 7200, fn() =>
        $this->client->getDailyForecast($lat, $lon)->throw()->json('daily', [])
        );

        return collect($data)
            ->take(7)
            ->map(fn($day) => ForecastTrendDto::fromArray($day))
            ->toArray();
    }

    /**
     * Aggregate daily wind speed & direction stats
     * @return array<DailyWindStatsDto>
     */
    public function getDailyWindStats(float $lat, float $lon): array
    {
        $data = Cache::remember("weather:daily:wind:{$lat},{$lon}", 7200, fn() =>
        $this->client->getDailyForecast($lat, $lon)->throw()->json('daily', [])
        );

        return collect($data)
            ->take(7)
            ->map(fn($day) => DailyWindStatsDto::fromArray($day))
            ->toArray();
    }

    public function fetchWeatherForecastData(float $lat = 0, float $lon = 0): array
    {
        try {
            $response = $this->client->getForecast($lat, $lon)->throw();
            return (array)ForecastTrendDto::fromArray($response->json());
        } catch (RequestException $e) {
            if ($e->response?->status() === 401) {
                abort(401, 'Unauthorized to forecast API');
            }
            Log::error('Forecast API error', [
                'message' => $e->getMessage(),
                'status'  => $e->response?->status(),
            ]);
            abort(502, 'Forecast API error');
        }
    }
}
