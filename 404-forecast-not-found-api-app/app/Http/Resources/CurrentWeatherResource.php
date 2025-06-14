<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentWeatherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource;

        return [
            'temperature' => $data['main']['temp'] ?? null,
            'feels_like'  => $data['main']['feels_like'] ?? null,
            'pressure'    => $data['main']['pressure'] ?? null,
            'humidity'    => $data['main']['humidity'] ?? null,
            'wind'        => [
                'speed' => $data['wind']['speed'] ?? null,
                'deg'   => $data['wind']['deg'] ?? null,
            ],
            'clouds'      => $data['clouds']['all'] ?? null,
            'weather'     => [
                'main'        => $data['weather'][0]['main'] ?? null,
                'description' => $data['weather'][0]['description'] ?? null,
                'icon'        => $data['weather'][0]['icon'] ?? null,
            ],
            'sunrise'     => isset($data['sys']['sunrise'])
                ? now()->setTimestamp($data['sys']['sunrise'])
                : null,
            'sunset'      => isset($data['sys']['sunset'])
                ? now()->setTimestamp($data['sys']['sunset'])
                : null,
            'fetched_at'  => now(),
        ];
    }
}
