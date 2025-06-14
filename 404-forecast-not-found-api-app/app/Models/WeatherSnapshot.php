<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class WeatherSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'temperature',
        'feels_like',
        'pressure',
        'humidity',
        'wind_speed',
        'wind_deg',
        'clouds',
        'weather_main',
        'weather_description',
        'weather_icon',
        'sunrise',
        'sunset',
        'data_source',
        'captured_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'sunrise'     => 'datetime',
        'sunset'      => 'datetime',
    ];

    /**
     * Persist a weather snapshot from API data.
     *
     * @param array      $data      Raw API response
     * @param Carbon     $timestamp When the data was fetched
     * @param int|null   $cityId    Optional related city ID
     * @return self
     */
    public static function store(array $data, Carbon $timestamp, int $cityId = null): self
    {
        return self::create([
            'city_id'            => $cityId,
            'temperature'        => Arr::get($data, 'main.temp'),
            'feels_like'         => Arr::get($data, 'main.feels_like'),
            'pressure'           => Arr::get($data, 'main.pressure'),
            'humidity'           => Arr::get($data, 'main.humidity'),
            'wind_speed'         => Arr::get($data, 'wind.speed'),
            'wind_deg'           => Arr::get($data, 'wind.deg'),
            'clouds'             => Arr::get($data, 'clouds.all'),
            'weather_main'       => Arr::get($data, 'weather.0.main'),
            'weather_description'=> Arr::get($data, 'weather.0.description'),
            'weather_icon'       => Arr::get($data, 'weather.0.icon'),
            'sunrise'            => Carbon::createFromTimestamp(Arr::get($data, 'sys.sunrise')),
            'sunset'             => Carbon::createFromTimestamp(Arr::get($data, 'sys.sunset')),
            'data_source'        => Arr::get($data, 'data_source', 'current'),
            'captured_at'        => $timestamp,
        ]);
    }
}
