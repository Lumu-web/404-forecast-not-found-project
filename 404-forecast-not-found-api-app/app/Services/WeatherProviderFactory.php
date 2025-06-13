<?php

namespace App\Services;

use App\Models\WeatherProvider;
use Illuminate\Contracts\Container\Container;

class WeatherProviderFactory
{
    public function __construct(protected Container $container) {}

    public function make(WeatherProvider $provider): WeatherProviderInterface
    {
        return match ($provider->code) {
            OpenWeatherMapService::PROVIDER_CODE => $this->container->make(OpenWeatherMapService::class),
            default => throw new \InvalidArgumentException("Unsupported provider [{$provider->code}]"),
        };
    }
}
