<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = ['name','province','country','lat','lon'];

    public function snapshots(): HasMany
    { return $this->hasMany(WeatherSnapshot::class); }
    public function forecasts(): HasMany
    { return $this->hasMany(WeatherForecast::class); }
    public function airReadings(): HasMany
    { return $this->hasMany(AirQualityReading::class); }
    public function importLogs(): HasMany
    { return $this->hasMany(WeatherImportLog::class); }
}
