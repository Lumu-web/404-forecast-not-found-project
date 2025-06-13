<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $location_id
 * @property int $weather_provider_id
 * @property string $date
 * @property float $high
 * @property float $low
 * @property float $precipitation_prob
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ForecastReading extends Model
{
    protected $fillable = [
        'location_id',
        'weather_provider_id',
        'date',
        'high',
        'low',
        'precipitation_prob',
    ];
}
