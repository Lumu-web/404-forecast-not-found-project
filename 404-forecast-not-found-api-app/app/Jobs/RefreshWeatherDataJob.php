<?php

namespace App\Jobs;

use App\Services\OpenWeatherMapService;
use App\Models\WeatherRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class RefreshWeatherDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $city;

    /**
     * @param string $city  The city name to refresh (e.g. "Cape Town")
     */
    public function __construct(string $city)
    {
        $this->city = $city;
    }

    /**
     * @param  OpenWeatherMapService  $weatherService
     * @return void
     * @throws RuntimeException
     */
    public function handle(OpenWeatherMapService $weatherService): void
    {
        $matches = $weatherService->fetchAutoCompleteCityList($this->city);
        if (empty($matches)) {
            throw new RuntimeException("Could not find coordinates for city: {$this->city}");
        }

        $coords = $matches[0];
        $lat = $coords['lat'];
        $lon = $coords['lon'];
        $payload = $weatherService->fetchCurrentWeather($lat, $lon);

        WeatherRecord::create([
            'city'      => $this->city,
            'timestamp' => now(),
            'data'      => $payload,
        ]);
    }
}
