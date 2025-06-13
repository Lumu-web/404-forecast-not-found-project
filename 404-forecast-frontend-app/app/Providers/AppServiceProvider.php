<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::macro('forecastTokenApiCall', function () {
            // Ensure the auth token is available in the session
            $token = session('auth_token');
            if (!$token) {
                throw new \Exception('Authentication token is not available in the session.');
            }
            return Http::withToken($token)->acceptJson();
        });
    }
}
