<?php

namespace App\Services;

use App\Clients\OpenWeatherMapClient;
use App\Models\WeatherRecord;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class OpenWeatherMapService extends WeatherProviderService
{
    const PROVIDER_CODE = 'openweathermap';
    const DEFAULT_LATITUDE = -33.962;
    const DEFAULT_LONGITUDE = 25.621;
    const DEFAULT_CITY = 'Gqeberha';
    const DEFAULT_COUNTRY = 'ZA';

    public function __construct(protected OpenWeatherMapClient $client)
    {
        $this->client->setProviderCode(self::PROVIDER_CODE);
    }

    /**
     * @throws Exception
     */
    public function fetchCurrentWeather(float $lat, float $lon, string $city = '', string $country = '', string $exclude = ''): array
    {
        if ($city && $country) {
            $cacheKey = $this->cacheKeyCurrentWeather($city, $country);
            return $this->getCachedData($cacheKey, function () use ($lat, $lon, $exclude) {
                $data = $this->client->getCurrentWeather($lat, $lon, $exclude);
                $data['dt'] = $this->formatToUseFriendlyDate($data['dt'] ?? null);
                return $data;
            });
        }

        $data = $this->client->getCurrentWeather($lat, $lon, $exclude);
        $data['dt'] = $this->formatToUseFriendlyDate($data['dt'] ?? null);
        return $data;
    }

    /**
     * @throws Exception
     */
    public function fetchWeatherForecast(float $lat, float $lon, string $city = '', string $country = ''): array
    {
        if ($city && $country) {
            $cacheKey = $this->cacheKeyWeatherForecast($city, $country);
            return $this->getCachedData($cacheKey, fn() => $this->client->getWeatherForecast($lat, $lon));
        }

        return $this->client->getWeatherForecast($lat, $lon);
    }

    /**
     * @throws Exception
     */
    public function fetchHistoricalWeatherForecast(float $lat, float $lon, string $timestamp): array
    {
        return $this->client->getHistoricalWeather($lat, $lon, $timestamp);
    }

    /**
     * @throws Exception
     */
    public function fetchAutoCompleteCityList(string $city): array
    {
        $cityList = $this->client->getAutoCompleteCityList($city);

        return array_map(fn($c) => [
            'name' => $c['name'],
            'state' => $c['state'] ?? null,
            'country' => $c['country'],
            'lat' => $c['lat'],
            'lon' => $c['lon'],
        ], $cityList);
    }

    public function fetchWeatherOverview(
        float $lat = self::DEFAULT_LATITUDE,
        float $lon = self::DEFAULT_LONGITUDE,
        string $city = self::DEFAULT_CITY,
        string $country = self::DEFAULT_COUNTRY
    ): array {
        return [
            'current' => $this->fetchCurrentWeather($lat, $lon, $city, $country),
            'forecast' => $this->fetchWeatherForecast($lat, $lon, $city, $country),
        ];
    }

    public function getDefaults(): array
    {
        return [
            'lat' => self::DEFAULT_LATITUDE,
            'lon' => self::DEFAULT_LONGITUDE,
            'city' => self::DEFAULT_CITY,
            'country' => self::DEFAULT_COUNTRY,
        ];
    }

    public function getWeatherProviderCode(): string
    {
        return self::PROVIDER_CODE;
    }

    public function getHistory(array|string $city, int $date): Collection
    {
        return WeatherRecord::where('city', $city)
            ->whereDate('timestamp', $date)
            ->get();
    }

    protected function cacheKeyCurrentWeather(string $city, string $country): string
    {
        return "current_weather_{$city}_{$country}";
    }

    protected function cacheKeyWeatherForecast(string $city, string $country): string
    {
        return "weather_forecast_{$city}_{$country}";
    }

    protected function formatToUseFriendlyDate(?int $timestamp): string
    {
        return Carbon::createFromTimestamp($timestamp ?? time(), 'Africa/Johannesburg')
            ->format('M j, Y \a\t H:i');
    }
}

