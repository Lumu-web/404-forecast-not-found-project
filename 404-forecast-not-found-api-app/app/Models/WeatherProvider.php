<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $code
 * @property int $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class WeatherProvider extends Model
{
    protected $fillable = [
        'name',
        'description',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function forecastReadings()
    {
        return $this->hasMany(ForecastReading::class);
    }

    public function currentReadings()
    {
        return $this->hasMany(CurrentReading::class);
    }

}
