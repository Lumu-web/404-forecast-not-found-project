<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WeatherForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'forecast_at',
        'temperature',
        'feels_like',
        'pressure',
        'humidity',
        'wind_speed',
        'wind_deg',
        'weather_main',
        'weather_description',
        'weather_icon',
        'source_batch_id',
    ];

    protected $casts = [
        'forecast_at' => 'datetime',
    ];

    /**
     * Persist a batch of forecast items from API response.
     *
     * @param array      $items      Array of raw forecast data items
     * @param Carbon|null $fetchedAt Optional timestamp when data was fetched
     * @param int|null   $cityId     Optional related city ID
     * @return int                  Number of records inserted
     */
    public static function storeBatch(array $items, Carbon $fetchedAt = null, int $cityId = null): int
    {
        $batchId   = Str::uuid()->toString();
        $timestamp = $fetchedAt ?? now();
        $count     = 0;

        foreach ($items as $item) {
            self::create([
                'city_id'             => $cityId,
                'forecast_at'         => Carbon::parse(Arr::get($item, 'dt_txt')),
                'temperature'         => Arr::get($item, 'main.temp'),
                'feels_like'          => Arr::get($item, 'main.feels_like'),
                'pressure'            => Arr::get($item, 'main.pressure'),
                'humidity'            => Arr::get($item, 'main.humidity'),
                'wind_speed'          => Arr::get($item, 'wind.speed'),
                'wind_deg'            => Arr::get($item, 'wind.deg'),
                'weather_main'        => Arr::get($item, 'weather.0.main'),
                'weather_description' => Arr::get($item, 'weather.0.description'),
                'weather_icon'        => Arr::get($item, 'weather.0.icon'),
                'source_batch_id'     => $batchId,
            ]);

            $count++;
        }

        return $count;
    }
}
