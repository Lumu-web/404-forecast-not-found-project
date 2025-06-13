<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(function () {
                require __DIR__.'/../routes/web.php';
                require __DIR__.'/../routes/auth.php';
                require __DIR__.'/../routes/weather.php';
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web([
            // Default web middleware (session, cookies, CSRF)
        ]);

        $middleware->api([
            // API middleware, if needed
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Customize exception handling here if needed
    })
    ->create();
