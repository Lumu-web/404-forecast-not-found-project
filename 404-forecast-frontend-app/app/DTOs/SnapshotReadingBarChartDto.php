<?php

namespace App\DTOs;

use Carbon\Carbon;

class snapshotReadingBarChartDto
{
    public function __construct(
        public string $name,
        public string $label,
        public int $temp,
        public int $feels,
        public float $humid,
        public float $pres
    ) {}

    public static function fromArray(array $data): self
    {
        $timestamp = $data['dt'] ?? null;
        $label = $timestamp
            ? Carbon::createFromTimestamp($timestamp)->toDateTimeString()
            : ($data['captured_at'] ?? 'Unknown');

        $tempK  = $data['temperature'] ?? $data['main']['temp'] ?? null;
        $feelsK = $data['feels_like'] ?? $data['main']['feels_like'] ?? null;

        return new self(
            name: $data['name'] ?? 'Unknown',
            label: $label,
            temp: isset($tempK) ? round($tempK - 273.15) : 0,
            feels: isset($feelsK) ? round($feelsK - 273.15) : 0,
            humid: (float)($data['humidity'] ?? $data['main']['humidity'] ?? 0),
            pres: (float)($data['pressure'] ?? $data['main']['pressure'] ?? 0),
        );
    }
}
