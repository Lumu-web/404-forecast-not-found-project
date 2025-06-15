<?php

namespace App\DTOs;

use Carbon\CarbonImmutable;

readonly class HourlyPopDto
{
    public CarbonImmutable $forecastAt;
    public float $pop;

    private function __construct(CarbonImmutable $forecastAt, float $pop)
    {
    }

    /**
     * Create from one of your forecast array items.
     *
     * @param array{
     *     forecast_at: string,
     *     pop:  float|int|string,
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            CarbonImmutable::parse($data['forecast_at']),
            (float)$data['pop']
        );
    }

    /**
     * Convert back to a simple array for JSON/JS.
     */
    public function toArray(): array
    {
        return [
            // forecast_at => “hour:minute”
            'forecast_at' => $this->forecastAt->format('H:i'),
            'feels_like' => $this->pop,
        ];
    }
}
