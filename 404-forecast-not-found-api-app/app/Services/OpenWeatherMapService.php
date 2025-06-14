<?php

namespace App\Services;

use App\Clients\OpenWeatherMapClient;
use App\Models\WeatherImportLog;
use App\Models\WeatherSnapshot;
use App\Models\WeatherForecast;
use RuntimeException;

class OpenWeatherMapService extends WeatherProviderService
{
    public const PROVIDER_CODE     = 'openweathermap';
    public const DEFAULT_LATITUDE  = -33.962;
    public const DEFAULT_LONGITUDE = 25.621;
    public const DEFAULT_CITY      = 'Gqeberha';
    public const DEFAULT_COUNTRY   = 'ZA';

    public function __construct(protected OpenWeatherMapClient $client)
    {
        $this->client->setProviderCode(self::PROVIDER_CODE);
    }

    public function fetchCurrentWeather(float $lat, float $lon, ?int $cityId = null, string $exclude = ''): array
    {
        $start = now();
        try {
            $data = $this->client->getCurrentWeather($lat, $lon, $exclude);
        } catch (\Throwable $e) {
            WeatherImportLog::failure('current', $e->getMessage(), $start);
            throw new RuntimeException($e->getMessage());
        }

        WeatherSnapshot::store($data, $start, $cityId);
        WeatherImportLog::success('current', $start, $cityId);

        return $data;
    }

    public function fetchWeatherForecast(float $lat, float $lon, ?int $city): array
    {
        $start = now();
        try {
            $response = $this->client->getWeatherForecast($lat, $lon);
        } catch (\Throwable $e) {
            WeatherImportLog::failure('forecast', $e->getMessage(), $start);
            throw new RuntimeException($e->getMessage());
        }

        WeatherForecast::storeBatch($response['list'], $start, $city);
        WeatherImportLog::success('forecast', $start, $city);

        return $response;
    }

    public function fetchAirQuality(float $lat, float $lon): array
    {
        // If you want to persist AQI, you could do so here:
        // $start = now();
        // $data  = $this->client->getAirQuality($lat, $lon);
        // AirQualityReading::storeFromApi($data, $start);
        // WeatherImportLog::success('air', $start);
        // return $data;

        return $this->client->getAirQuality($lat, $lon);
    }

    public function fetchAutoCompleteCityList(string $query): array
    {
        return $this->client->getAutoCompleteCityList($query);
    }

    public function fetchWeatherOverview(
        float  $lat,
        float  $lon,
        string $city,
        string $country
    ): array {
        return [
            'current'  => $this->fetchCurrentWeather($lat, $lon, ''),
            'forecast' => $this->fetchWeatherForecast($lat, $lon),
        ];
    }

    public function getDefaults(): array
    {
        return [
            'lat'     => self::DEFAULT_LATITUDE,
            'lon'     => self::DEFAULT_LONGITUDE,
            'city'    => self::DEFAULT_CITY,
            'country' => self::DEFAULT_COUNTRY,
        ];
    }

    public function getWeatherProviderCode(): string
    {
        return self::PROVIDER_CODE;
    }
}
