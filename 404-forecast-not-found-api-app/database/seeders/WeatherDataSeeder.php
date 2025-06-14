<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\City;
use App\Models\WeatherSnapshot;
use App\Models\WeatherForecast;
use App\Models\AirQualityReading;
use App\Models\WeatherImportLog;

class WeatherDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('seeders/data/za_cities.json'));
        $cities = json_decode($json, true);

        foreach ($cities as $c) {
            $city = City::updateOrCreate(
                [
                    'name'      => $c['name'],
                    'province'  => $c['state'] ?? null,
                    'country'   => $c['country'],
                    'lat'       => $c['coord']['lat'],
                    'lon'       => $c['coord']['lon'],
                ]
            );
        }

        foreach (City::all() as $city) {
            WeatherSnapshot::create([
                'city_id'            => $city->id,
                'temperature'        => rand(-5, 35),
                'feels_like'         => rand(-7, 37),
                'pressure'           => rand(980, 1030),
                'humidity'           => rand(10, 100),
                'wind_speed'         => rand(0, 15) / 1.0,
                'wind_deg'           => rand(0, 360),
                'clouds'             => rand(0, 100),
                'weather_main'       => 'Clear',
                'weather_description'=> 'clear sky',
                'weather_icon'       => '01d',
                'sunrise'            => now()->subHours(6),
                'sunset'             => now()->addHours(6),
                'data_source'        => 'current',
                'captured_at'        => now(),
            ]);
        }

        foreach (City::limit(5)->get() as $city) {
            $batchId = (string) \Illuminate\Support\Str::uuid();
            for ($i = 1; $i <= 5; $i++) {
                $forecastAt = now()->addDays($i);

                WeatherForecast::create([
                    'city_id'            => $city->id,
                    'forecast_at'        => $forecastAt,
                    'temperature'        => rand(-5, 35),
                    'feels_like'         => rand(-7, 37),
                    'pressure'           => rand(980, 1030),
                    'humidity'           => rand(10, 100),
                    'wind_speed'         => rand(0, 15) / 1.0,
                    'wind_deg'           => rand(0, 360),
                    'weather_main'       => 'Clouds',
                    'weather_description'=> 'few clouds',
                    'weather_icon'       => '02d',
                    'source_batch_id'    => $batchId,
                ]);
            }
        }

        foreach (City::limit(5)->get() as $city) {
            AirQualityReading::create([
                'city_id'     => $city->id,
                'aqi'         => rand(1, 5),
                'co'          => rand(100, 500) / 100.0,
                'no'          => rand(10, 50) / 10.0,
                'no2'         => rand(10, 50) / 10.0,
                'o3'          => rand(10, 70) / 10.0,
                'so2'         => rand(1, 10) / 1.0,
                'pm2_5'       => rand(0, 50) / 1.0,
                'pm10'        => rand(0, 70) / 1.0,
                'nh3'         => rand(0, 20) / 1.0,
                'captured_at' => now(),
            ]);
        }

        WeatherImportLog::create([
            'source'        => 'current',
            'city_id'       => null,
            'success'       => true,
            'error_message' => null,
            'pulled_at'     => now(),
        ]);
    }
}
