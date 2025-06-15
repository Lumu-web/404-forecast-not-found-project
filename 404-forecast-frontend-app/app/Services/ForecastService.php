<?php

namespace App\Services;

use App\Clients\ForecastClient;
use App\DTOs\ForecastTrendDto;
use App\DTOs\snapshotReadingBarChartDto;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class ForecastService
{
    public function __construct(protected ForecastClient $client) {}

    public function fetchCurrentWeatherBarChartData(float $lat = 0, float $lon = 0): array
    {
        try {
            $response = $this->fetchCurrentWeatherData($lat, $lon);
            return (array)snapshotReadingBarChartDto::fromArray($response);
        } catch (RequestException $e) {
            Log::error('Current weather bar chart API error', [
                'message' => $e->getMessage(),
                'status'  => $e->response?->status(),
            ]);
            return [];
        }
    }
    public function fetchCurrentWeatherData(float $lat = 0, float $lon = 0): array
    {
        try {
            $response = $this->client->getCurrentWeather($lat, $lon)->throw();
           return $response->json();
        } catch (RequestException $e) {
            Log::error('Current weather API error', [
                'message' => $e->getMessage(),
                'status'  => $e->response?->status(),
            ]);
            return [];
        }
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

    public function fetchWeatherOverviewData(?float $lat = null, ?float $lon = null): array
    {
        try {
            $response = $this->client->getOverview($lat, $lon)->throw();
            return $response->json();
        } catch (RequestException $e) {
            if ($e->response?->status() === 401) {
                abort(401, 'Unauthorized');
            }
            Log::error('Overview API error', [
                'message' => $e->getMessage(),
                'status'  => $e->response?->status(),
            ]);
            abort(500, 'Error fetching weather data');
        }
    }

    public function getGuestSampleData(): array
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
