<?php

namespace App\Clients;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

abstract class BaseProviderClient
{
    /**
     * @var string
     */
    protected string $providerCode;
    /**
     * @var int
     */
    protected int    $dailyLimit;
    /**
     * @var string
     */
    protected string $baseUrl;
    /**
     * @var string
     */
    protected string $apiKey;

    /**
     * @param string $baseUrl
     * @param string $apiKey
     * @param int $dailyLimit
     */
    public function __construct(string $baseUrl, string $apiKey, int $dailyLimit = 1000)
    {
        $this->baseUrl    = rtrim($baseUrl, '/');
        $this->apiKey     = $apiKey;
        $this->dailyLimit = $dailyLimit;
    }

    /**
     * @param string $code
     * @return void
     */
    public function setProviderCode(string $code): void
    {
        $this->providerCode = $code;
    }

    /**
     * @return void
     */
    protected function trackApiHit(): void
    {
        $key  = "api_hits_{$this->providerCode}_" . now()->toDateString();
        $hits = Cache::increment($key);
        if ($hits === 1) {
            Cache::put($key, 1, now()->endOfDay());
        }
        if ($hits > $this->dailyLimit) {
            Log::warning("Rate limit exceeded: {$this->providerCode}");
            throw new RuntimeException('Rate limit exceeded');
        }
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws ConnectionException
     */
    protected function request(string $endpoint, array $params = []): array
    {
        $this->trackApiHit();
        $response = Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->get($endpoint, array_merge($params, ['appid' => $this->apiKey]));
        if ($response->failed()) {
            Log::error('Provider request failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new RuntimeException("HTTP {$response->status()} error");
        }
        return $response->json();
    }

    /**
     * @param string $city
     * @param int $limit
     * @return array
     * @throws ConnectionException
     */
    public function getAutoCompleteCityList(string $city, int $limit = 5): array
    {
        $endpoint = 'https://api.openweathermap.org/geo/1.0/direct';
        $params   = ['q' => $city, 'limit' => $limit, 'appid' => $this->apiKey];
        $response = Http::acceptJson()->get($endpoint, $params);
        if ($response->failed()) {
            Log::error('Geocode failed', ['body' => $response->body()]);
            throw new RuntimeException('Geocoding error');
        }
        return $response->json();
    }
}
