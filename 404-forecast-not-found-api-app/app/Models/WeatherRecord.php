<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherRecord extends Model
{
    protected $fillable = ['city','timestamp','data'];
    protected $casts    = ['timestamp' => 'datetime','data' => 'array'];
}
