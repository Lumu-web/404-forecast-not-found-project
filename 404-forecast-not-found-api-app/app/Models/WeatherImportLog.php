<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class WeatherImportLog extends Model
{
    use HasFactory;

    protected $table = 'weather_import_logs';

    protected $fillable = [
        'source',
        'city_id',
        'success',
        'error_message',
        'pulled_at',
    ];

    protected $casts = [
        'city_id'      => 'integer',
        'success'      => 'boolean',
        'pulled_at'    => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Log a successful import.
     *
     * @param string $source Source identifier (e.g. 'current', 'forecast', 'air')
     * @param Carbon|null $timestamp When the import was pulled
     * @param int|null $cityId Optional related city ID
     * @return self
     */
    public static function success(string $source, Carbon $timestamp = null, int $cityId = null): self
    {
        return self::create([
            'source'      => $source,
            'city_id'     => $cityId,
            'success'     => true,
            'error_message' => null,
            'pulled_at'   => $timestamp ?? now(),
        ]);
    }

    /**
     * Log a failed import.
     *
     * @param string $source Source identifier
     * @param string $errorMessage Error message
     * @param Carbon|null $timestamp When the import was attempted
     * @param int|null $cityId Optional related city ID
     * @return self
     */
    public static function failure(string $source, string $errorMessage, Carbon $timestamp = null, int $cityId = null): self
    {
        return self::create([
            'source'      => $source,
            'city_id'     => $cityId,
            'success'     => false,
            'error_message' => $errorMessage,
            'pulled_at'   => $timestamp ?? now(),
        ]);
    }
}
