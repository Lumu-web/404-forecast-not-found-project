<?php

namespace App\DTOs;

use Carbon\Carbon;

class ForecastTrendDto
{
    public function __construct(
        public string $label,
        public int $temperature,
        public int $feelsLike
    ) {}

    /**
     * Transform a single forecast array into DTO
     *
     * @param  array  $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $dt = Carbon::parse($data['forecast_at']);
        $label = $dt->format('Y-m-d H:i');

        $temp = isset($data['temperature'])
            ? (int) round($data['temperature'] - 273.15)
            : 0;

        $feels = isset($data['feels_like'])
            ? (int) round($data['feels_like'] - 273.15)
            : 0;

        return new self(
            label: $label,
            temperature: $temp,
            feelsLike: $feels
        );
    }
}
