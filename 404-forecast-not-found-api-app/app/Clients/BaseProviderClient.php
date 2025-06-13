<?php

namespace App\Clients;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class BaseProviderClient
{
    protected string $providerCode;
    protected int $dailyLimit = 1000;

    public function setProviderCode(string $providerCode): void
    {
        $this->providerCode = $providerCode;
    }

    public function setDailyLimit(int $limit): void
    {
        $this->dailyLimit = $limit;
    }

    protected function trackApiHit(): void
    {
        $key = "api_hits_{$this->providerCode}_" . now()->toDateString();
        $hits = Cache::increment($key);

        if ($hits === 1) {
            Cache::put($key, 1, now()->endOfDay());
        }

        if ($hits > $this->dailyLimit) {
            Log::warning("API rate limit exceeded for provider: {$this->providerCode}");
            throw new Exception("Rate limit exceeded for provider: {$this->providerCode}");
        }
    }

    /**
     * @throws Exception
     */
    protected function get(string $endpoint, array $queryParams = []): Response
    {
        $response = Http::get("{$this->baseUrl}/{$endpoint}", $queryParams);
        if ($response->ok()) {
            $this->trackApiHit();
        }
        return $response;
    }

    public function getAutoCompleteCityList(string $city)
    {
        $response = Http::get('https://api.openweathermap.org/geo/1.0/direct', [
            'q'     => $city,
            'limit' => 5,
            'appid' => $this->apiKey,
        ]);

        if ($response->failed()) {
            throw new Exception("Failed to fetch weather data: " . $response->body());
        }

        return $response->json();
    }
}
