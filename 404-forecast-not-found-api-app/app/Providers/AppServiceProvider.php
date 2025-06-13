<?php

namespace App\Providers;

use App\Clients\OpenWeatherMapClient;
use App\Services\OpenWeatherMapService;
use App\Services\WeatherProviderFactory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(ValidationServiceProvider::class);

        $this->app->singleton(OpenWeatherMapClient::class);
        $this->app->singleton(OpenWeatherMapService::class);
        $this->app->singleton(WeatherProviderFactory::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        //Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class)
    }
}
