<?php

namespace App\DTOs;

use Carbon\Carbon;

class DailyWindStatsDto
{
    public function __construct(
        public string $date,
        public float $speed,
        public int $deg
    ) {}

    /**
     * Transform a single daily forecast array into wind stats DTO
     *
     * @param  array  $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $dt = Carbon::parse($data['forecast_at'] ?? $data['dt'] ?? now());
        $date = $dt->format('Y-m-d');

        $wind = $data['wind'] ?? [];
        $speed = isset($wind['speed']) ? (float) $wind['speed'] : 0.0;
        $deg   = isset($wind['deg'])   ? (int) $wind['deg']     : 0;

        return new self(
            date:  $date,
            speed: $speed,
            deg:   $deg
        );
    }
}
