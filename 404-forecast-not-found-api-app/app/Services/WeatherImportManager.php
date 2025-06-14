<?php

namespace App\Services\Weather;

use App\Models\City;

class WeatherImportManager
{
    protected CurrentWeatherImporter $current;
    protected ForecastImporter $forecast;
    protected AirQualityImporter $air;

    public function __construct(
        CurrentWeatherImporter $current,
        ForecastImporter $forecast,
        AirQualityImporter $air
    ) {
        $this->current  = $current;
        $this->forecast = $forecast;
        $this->air      = $air;
    }

    /**
     * Run full import for a collection of cities.
     *
     * @param \Illuminate\Database\Eloquent\Collection|City[] $cities
     */
    public function importAll($cities): void
    {
        foreach ($cities as $city) {
            $this->current->import($city);
            $this->forecast->import($city);
            $this->air->import($city);
        }
    }
}

