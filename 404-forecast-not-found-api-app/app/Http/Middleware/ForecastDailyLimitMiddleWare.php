<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ForecastDailyLimitMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return JsonResponse|mixed|Response
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $key = 'open_weather_map_api_daily_count';
        $limit = 1000;

        $count = Cache::get($key, 0);

        if ($count >= $limit) {
            return response()->json([
                'message' => 'Daily API request limit reached. Try again tomorrow.'
            ], 429);
        }
        Cache::put($key, $count + 1, now()->endOfDay());

        return $next($request);
    }
}
