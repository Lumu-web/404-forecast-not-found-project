<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ForecastResource extends JsonResource
{
    /**
     * Transform the forecast item into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $data = $this->resource;

        return [
            'forecast_at' => isset($data['dt_txt'])
                ? Carbon::parse($data['dt_txt'])
                : null,
            'temperature' => $data['main']['temp'] ?? null,
            'feels_like'  => $data['main']['feels_like'] ?? null,
            'pressure'    => $data['main']['pressure'] ?? null,
            'humidity'    => $data['main']['humidity'] ?? null,
            'wind'        => [
                'speed' => $data['wind']['speed'] ?? null,
                'deg'   => $data['wind']['deg'] ?? null,
            ],
            'weather'     => [
                'main'        => $data['weather'][0]['main'] ?? null,
                'description' => $data['weather'][0]['description'] ?? null,
                'icon'        => $data['weather'][0]['icon'] ?? null,
            ],
        ];
    }
}
