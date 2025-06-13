<?php
namespace App\Jobs;

use App\Services\OpenWeatherMapService;
use App\Models\WeatherRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class RefreshWeatherDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $location;

    public function __construct(string $location)
    {
        $this->location = $location;
    }

    public function handle(OpenWeatherMapService $weatherService)
    {
        $data = $weatherService->fetchWeatherFor($this->location);

        // Save the relevant data
        WeatherRecord::create([
            'location'     => $this->location,
            'temperature'  => $data['temp'],
            'humidity'     => $data['humidity'],
            'pressure'     => $data['pressure'],
            'fetched_at'   => now(),
        ]);
    }
}
