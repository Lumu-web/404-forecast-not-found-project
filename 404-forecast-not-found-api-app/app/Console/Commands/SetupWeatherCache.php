<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Services\OpenWeatherMapService;

class SetupWeatherCache extends Command
{
    protected $name = 'weather:cache';

    protected $description = 'Fetch and cache weather data for Port Elizabeth, ZA';

    protected OpenWeatherMapService $weatherService;

    public function __construct(OpenWeatherMapService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $latitude = OpenWeatherMapService::DEFAULT_LATITUDE; // Latitude for Port Elizabeth
        $longitude = OpenWeatherMapService::DEFAULT_LONGITUDE; // Longitude for Port Elizabeth
        $city = OpenWeatherMapService::DEFAULT_CITY;
        $country = OpenWeatherMapService::DEFAULT_COUNTRY;
        $currentWeatherData = $this->weatherService->fetchCurrentWeather(
            $latitude,
            $longitude,
            $city,
            $country
        );
        $forecastWeatherData = $this->weatherService->fetchWeatherForecast(
            $latitude,
            $longitude,
            $city,
            $country
        );

        $this->info("Weather data for {$city}, {$country} cached successfully.");
    }
}

