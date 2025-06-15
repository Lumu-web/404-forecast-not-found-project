<?php

namespace App\Services;

use App\Clients\ForecastClient;
use App\DTOs\snapshotReadingBarChartDto;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrentWeatherService
{
    public function __construct(protected ForecastClient $client) {}

    public function fetchCurrentWeatherBarChartData(float $lat = 0, float $lon = 0): array
    {
        return $this->getSnapshot($lat, $lon);
    }
    /**
     * Get the latest current weather snapshot
     */
    private function getSnapshot(float $lat, float $lon): array
    {
        return $this->fetchCurrentWeatherData($lat, $lon);
    }

    /**
     * Extract sunrise and sunset times from current weather API
     */
    public function getSunriseSunset(float $lat, float $lon): array
    {
        $data = $this->getSnapshot($lat, $lon);
        return [
            'sunrise' => $data['sys']['sunrise'] ?? null,
            'sunset'  => $data['sys']['sunset']  ?? null,
        ];
    }

    /**
     * Fetch UV index via OneCall "ultraviolet" endpoint
     */
    public function getUvIndex(float $lat, float $lon): int
    {
        $data = $this->getSnapshot($lat, $lon);
        return (int) Arr::get($data, 'current.uvi', 0);
    }

    public function fetchCurrentWeatherData(float $lat = 0, float $lon = 0): array
    {
        try {
            return Cache::remember("weather:current:{$lat},{$lon}", 300, fn() =>
            $this->client->getCurrentWeather($lat, $lon)
                ->throw()
                ->json()
            );
        } catch (RequestException $e) {
            Log::error('Current weather API error', [
                'message' => $e->getMessage(),
                'status'  => $e->response?->status(),
            ]);
            return [];
        }
    }

    public function getGuestCurrentSnapShotSampleData(): array
    {
        try {
            $response = $this->client->getGuestCurrentSnapshotSampleData()->throw();
            return (array)snapshotReadingBarChartDto::fromArray($response->json());
        } catch (RequestException $e) {
            Log::error('Guest sample data error', [
                'message' => $e->getMessage(),
                'status'  => $e->response?->status(),
            ]);
            return [];
        }
    }
}
