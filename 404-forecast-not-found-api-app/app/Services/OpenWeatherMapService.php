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
    public const DEFAULT_CITY      = 'Port Elizabeth';
    public const DEFAULT_COUNTRY   = 'ZA';

    public function __construct(protected OpenWeatherMapClient $client)
    {
        $this->client->setProviderCode(self::PROVIDER_CODE);
    }

    public function fetchCurrentWeather(
        float  $lat,
        float  $lon,
        ?int   $cityId  = null,
        string $exclude = ''
    ): array {
        return $this->cacheCall(
            prefix: 'current_weather',
            params: [
                'lat'     => $lat,
                'lon'     => $lon,
                'exclude' => $exclude,
                'cityId'  => $cityId,
            ],
            callback: function () use ($lat, $lon, $exclude, $cityId) {
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
        );
    }

    public function fetchWeatherForecast(
        float $lat,
        float $lon,
        ?int  $cityId = null
    ): array {
        return $this->cacheCall(
            prefix: 'weather_forecast',
            params: [
                'lat'    => $lat,
                'lon'    => $lon,
                'cityId' => $cityId,
            ],
            callback: function () use ($lat, $lon, $cityId) {
                $start = now();
                try {
                    $response = $this->client->getWeatherForecast($lat, $lon);
                } catch (\Throwable $e) {
                    WeatherImportLog::failure('forecast', $e->getMessage(), $start);
                    throw new RuntimeException($e->getMessage());
                }

                WeatherForecast::storeBatch($response['list'], $start, $cityId);
                WeatherImportLog::success('forecast', $start, $cityId);

                return $response;
            },
            minutes: 30
        );
    }

    public function fetchAirQuality(float $lat, float $lon): array
    {
        return $this->cacheCall(
            prefix: 'air_quality',
            params: [
                'lat' => $lat,
                'lon' => $lon,
            ],
            callback: fn() => $this->client->getAirQuality($lat, $lon)
        );
    }

    public function fetchAutoCompleteCityList(string $query): array
    {
        return $this->cacheCall(
            prefix: 'autocomplete_city',
            params: ['query' => $query],
            callback: fn() => $this->client->getAutoCompleteCityList($query),
            minutes: 0.3
        );
    }

    public function fetchGuestWeatherOverview(
        float  $lat,
        float  $lon,
        ?int   $cityId = null
    ): array {
        return $this->fetchCurrentWeather($lat, $lon, $cityId);
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
