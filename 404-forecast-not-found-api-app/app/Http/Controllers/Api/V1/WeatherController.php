<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WeatherProvider;
use App\Services\WeatherProviderFactory;
use App\Services\WeatherProviderInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected WeatherProviderInterface $weatherService;
    public function __construct(
        protected WeatherProviderFactory $weatherServiceFactory,
    ) {
        $provider = WeatherProvider::where('is_active', 1)->firstOrFail();
        if (!$provider) {
            throw new \Exception('No active weather provider found');
        }
        $this->weatherService = $weatherServiceFactory->make($provider);
    }

    public function fetchCurrentWeather(Request $request): JsonResponse
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        $exclude = $request->query('exclude', '');
        if (!$lat || !$lon) {
            return response()->json(['error' => 'Latitude and longitude parameters are required'], 400);
        }

        try {
            $currentWeather = $this->weatherService->fetchCurrentWeather((float) $lat, (float) $lon, $exclude);
            return response()->json($currentWeather);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @throws \Exception
     */
    public function getWeatherForecast(Request $request): JsonResponse
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        if (!$lat || !$lon) {
            return response()->json(['error' => 'Latitude and longitude parameters are required'], 400);
        }

        $forecast = $this->weatherService->fetchWeatherForecast((float) $lat, (float) $lon);

        return response()->json($forecast);
    }

    public function getHistoricalWeatherForecast(Request $request): JsonResponse
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        $date = $request->query('date');
        if (!$lat || !$lon || !$date) {
            return response()->json(['error' => 'Latitude/longitude and dateTime parameters are required'], 400);
        }

        $lat = round($lat, 2);
        $lon = round($lon, 2);

        $history = $this->weatherService->fetchHistoricalWeatherForecast($lat, $lon, $date);
        return response()->json($history);
    }

    /**
     * @throws \Exception
     */
    public function getAutocompleteCity(Request $request): JsonResponse
    {
        $query = $request->input('city');
        $cityList = $this->weatherService->fetchAutoCompleteCityList($query);
        return response()->json($cityList);
    }

    public function getWeatherOverview(Request $request): JsonResponse
    {
        $defaults = $this->weatherService->getDefaults();

        $lat = $request->query('lat') !== null ? (float)$request->query('lat') : $defaults['lat'];
        $lon = $request->query('lon') !== null ? (float)$request->query('lon') : $defaults['lon'];
        $city = $request->query('city', $defaults['city']);
        $country = $request->query('country', $defaults['country']);

        try {
            $overview = $this->weatherService->fetchWeatherOverview($lat, $lon, $city, $country);
            return response()->json($overview);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getGuestWeatherOverview(): JsonResponse
    {
        $defaults = $this->weatherService->getDefaults();
        [
            'lat' => $lat,
            'lon' => $lon,
            'city' => $city,
            'country' => $country,
        ] = $defaults;

        try {
            $overview = $this->weatherService->fetchWeatherOverview($lat, $lon, $city, $country);
            return response()->json($overview);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
