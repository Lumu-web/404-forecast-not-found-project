<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class HourlyForecastResource extends JsonResource
{
    /**
     * Transform the forecast item into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        // Resource is expected is a resource from ForecastResource
        $nowHour = Carbon::now()->hour;

        $col = collect($this->resource);

        $startIndex = $col->search(function (array $item) use ($nowHour) {
            $dt = Carbon::parse($item['forecast_at']);
            return $dt->hour === $nowHour;
        });

        if ($startIndex === false) {
            $startIndex = 0;
        }

        return $col
            ->slice($startIndex, 3)
            ->values()
            ->toArray();
    }
}
