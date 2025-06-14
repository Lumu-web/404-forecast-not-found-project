<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeatherRequest;
use App\Http\Requests\CityAutocompleteRequest;
use App\Http\Requests\WeatherOverviewRequest;
use App\Http\Resources\CurrentWeatherResource;
use App\Http\Resources\ForecastResource;
use App\Models\City;
use App\Models\WeatherProvider;
use App\Services\WeatherProviderFactory;
use App\Services\WeatherProviderInterface;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    private WeatherProviderInterface $weatherService;

    public function __construct(WeatherProviderFactory $factory)
    {
        $provider = WeatherProvider::active()->firstOrFail();
        $this->weatherService = $factory->make($provider);
    }

    /**
     * Fetch current weather by lat/lon, city name, or city_id.
     */
    public function fetchCurrentWeather(WeatherRequest $request): JsonResponse
    {
        [$cityId, $lat, $lon] = $this->resolveLocation(
            $request->input('city_id'),
            $request->input('city'),
            $request->input('lat'),
            $request->input('lon')
        );

        $data = $this->weatherService->fetchCurrentWeather(
            $lat,
            $lon,
            $cityId,
            $request->input('exclude', '')
        );

        return CurrentWeatherResource::make($data)->response();
    }

    /**
     * Fetch weather forecast by lat/lon, city name, or city_id.
     */
    public function getWeatherForecast(WeatherRequest $request): JsonResponse
    {
        [$cityId, $lat, $lon] = $this->resolveLocation(
            $request->input('city_id'),
            $request->input('city'),
            $request->input('lat'),
            $request->input('lon')
        );

        $response = $this->weatherService->fetchWeatherForecast(
            $lat,
            $lon,
            $cityId
        );

        return ForecastResource::collection($response['list'])->response();
    }

    public function getAutocompleteCity(CityAutocompleteRequest $request): JsonResponse
    {
        $cities = $this->weatherService->fetchAutoCompleteCityList(
            $request->input('city')
        );
        return response()->json($cities);
    }

    public function getWeatherOverview(WeatherOverviewRequest $request): JsonResponse
    {
        [$cityId, $lat, $lon] = $this->resolveLocation(
            $request->input('city_id'),
            $request->input('city'),
            $request->input('lat'),
            $request->input('lon')
        );

        $defaults = $this->weatherService->getDefaults();
        $overview = $this->weatherService->fetchWeatherOverview(
            $lat ?? $defaults['lat'],
            $lon ?? $defaults['lon'],
            $request->input('city', $defaults['city']),
            $request->input('country', $defaults['country'])
        );

        return response()->json($overview);
    }

    /**
     * Resolve input to a valid City and coordinates.
     *
     * @param int|null        $cityId
     * @param string|null     $cityName
     * @param float|string|null $lat
     * @param float|string|null $lon
     * @return array [city_id, lat, lon]
     */
    protected function resolveLocation(?int $cityId, ?string $cityName, float|string|null $lat, float|string|null $lon): array
    {
        // Cast latitude/longitude to float if provided
        $lat = $lat !== null ? (float)$lat : null;
        $lon = $lon !== null ? (float)$lon : null;

        if ($cityId) {
            $city = City::findOrFail($cityId);
        } elseif ($cityName) {
            $city = City::where('name', $cityName)->firstOrFail();
        } elseif ($lat !== null && $lon !== null) {
            $city = City::where('lat', $lat)
                ->where('lon', $lon)
                ->first();

            if (! $city) {
                $city = City::create([
                    'lat'     => $lat,
                    'lon'     => $lon,
                    'name'    => 'Custom',
                    'country' => '',
                ]);
            }
        } else {
            abort(400, 'Must provide city_id, city name, or lat and lon.');
        }

        return [$city->id, $city->lat, $city->lon];
    }
}
