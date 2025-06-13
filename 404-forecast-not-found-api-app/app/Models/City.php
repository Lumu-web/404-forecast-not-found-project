<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class City
 *
 * @property int $id
 * @property string $name
 * @property string|null $province
 * @property string $country
 * @property float $lat
 * @property float $lon
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class City extends Model
{
    protected $fillable = [
        'name',
        'country',
        'province',
        'lat',
        'lon',
    ];
}
