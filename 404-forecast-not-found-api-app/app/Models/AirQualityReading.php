<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class AirQualityReading extends Model
{
    use HasFactory;

    protected $table = 'air_quality_readings';

    protected $fillable = [
        'city_id',
        'aqi',
        'co',
        'no',
        'no2',
        'o3',
        'so2',
        'pm2_5',
        'pm10',
        'nh3',
        'captured_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
    ];

    /**
     * Persist an air quality reading from API response.
     *
     * @param array      $apiResponse Raw API data containing a 'list' array
     * @param Carbon     $timestamp   When the data was fetched
     * @param int|null   $cityId      Optional related city ID
     * @return self
     */
    public static function storeFromApi(array $apiResponse, Carbon $timestamp, int $cityId = null): self
    {
        $item = $apiResponse['list'][0] ?? [];

        return self::create([
            'city_id'     => $cityId,
            'aqi'         => Arr::get($item, 'main.aqi'),
            'co'          => Arr::get($item, 'components.co'),
            'no'          => Arr::get($item, 'components.no'),
            'no2'         => Arr::get($item, 'components.no2'),
            'o3'          => Arr::get($item, 'components.o3'),
            'so2'         => Arr::get($item, 'components.so2'),
            'pm2_5'       => Arr::get($item, 'components.pm2_5'),
            'pm10'        => Arr::get($item, 'components.pm10'),
            'nh3'         => Arr::get($item, 'components.nh3'),
            'captured_at' => $timestamp,
        ]);
    }
}
