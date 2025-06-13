<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property int $location_id
 * @property int $weather_provider_id
 * @property string $timestamp
 * @property float $temperature
 * @property float $humidity
 * @property float $wind_speed
 * @property string $conditions
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin \Eloquent
 */
class CurrentReading extends Model
{
    protected $fillable = [
        'location_id',
        'weather_provider_id',
        'timestamp',
        'temperature',
        'humidity',
        'wind_speed',
        'conditions',
    ];
}
