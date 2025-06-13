<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use JsonMachine\Items;

class ImportZACities extends Command
{
    protected $signature = 'app:import-za-cities';
    protected $description = 'Download and import ZA cities from OpenWeatherMap bulk city list.';

    public function handle(): int
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $storagePath = storage_path('app/data');
        $gzPath = "{$storagePath}/city.list.json.gz";
        $jsonPath = "{$storagePath}/city.list.json";

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $this->info('📥 Downloading city.list.json.gz from OpenWeatherMap...');

        $response = Http::withOptions(['sink' => $gzPath])
            ->get('http://bulk.openweathermap.org/sample/city.list.json.gz');

        if ($response->status() !== 200) {
            $this->error("❌ Failed to download city list. Status: {$response->status()}");
            return self::FAILURE;
        }

        $this->info('📦 Extracting city.list.json...');
        $json = gzdecode(file_get_contents($gzPath));
        file_put_contents($jsonPath, $json);
        unlink($gzPath);
        $this->info("✅ Extracted to: {$jsonPath}");

        $this->info('🔍 Streaming and importing ZA cities...');
        $zaCityCount = 0;

        foreach (Items::fromFile($jsonPath) as $cityData) {
            if (($cityData->country ?? null) !== 'ZA') {
                continue;
            }

            City::updateOrCreate(
                [
                    'name' => $cityData->name,
                    'lat'  => $cityData->coord->lat,
                    'lon'  => $cityData->coord->lon,
                ],
                [
                    'province' => $cityData->state ?? null,
                    'country'  => 'ZA',
                ]
            );

            $zaCityCount++;
        }

        $this->info("✅ Successfully imported {$zaCityCount} ZA cities.");
        return self::SUCCESS;
    }
}
// This file is part of the 404 Forecast Not Found API App.
