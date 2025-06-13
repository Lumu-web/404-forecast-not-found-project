<?php namespace App\Models;

// app/Models/WeatherRecord.php
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WeatherRecord query()
 * @mixin \Eloquent
 */
class WeatherRecord extends Model
{
    protected $fillable = ['city', 'temperature', 'humidity', 'timestamp'];
}
