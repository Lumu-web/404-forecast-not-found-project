<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDummy
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Just dump something or log to confirm this middleware runs
        // You can comment out the line below if you want to avoid halting the request
        // dd('CheckDummy Middleware hit!');

        // Or log it:
        \Log::info('CheckDummy Middleware was called');

        // You can also add a header for easy checking:
        $response = $next($request);
        $response->headers->set('X-Dummy-Middleware', 'Passed');

        return $response;
    }
}
