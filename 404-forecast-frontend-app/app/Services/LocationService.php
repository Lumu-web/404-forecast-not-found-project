<?php

namespace App\Services;

use App\Clients\ForecastClient;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LocationService
{
    public function __construct(protected ForecastClient $client) {}

    /**
     * Fetch city location suggestions from Geo endpoint
     * @return array
     */
    public function getCityLocations(string $city): array
    {
        return Cache::remember("weather:locations:{$city}", 3600, fn() =>
        $this->client->getCityLocations($city)
            ->throw()
            ->json()
        );
    }

    public function fetchCityLocationsData(string $city): array
    {
        try {
            $response = $this->client->getCityLocations($city)->throw();
            return $response->json();
        } catch (RequestException $e) {
            Log::error('City locations API error', [
                'message' => $e->getMessage(),
                'status'  => $e->response?->status(),
            ]);
            return [];
        }
    }
}
