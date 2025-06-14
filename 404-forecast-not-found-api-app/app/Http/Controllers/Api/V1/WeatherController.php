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
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    private WeatherProviderInterface $weatherService;

    public function __construct(WeatherProviderFactory $factory)
    {
        $provider             = WeatherProvider::active()->firstOrFail();
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

    /**
     * Autocomplete on city name.
     */
    public function getAutocompleteCity(CityAutocompleteRequest $request): JsonResponse
    {
        $cities = $this->weatherService->fetchAutoCompleteCityList(
            $request->input('city')
        );

        return response()->json($cities);
    }

    /**
     * Guest overview: uses default lat/lon/city/country.
     */
    public function getGuestWeatherOverview(): JsonResponse
    {

        // pull in our defaults
        ['lat'     => $lat,
            'lon'     => $lon,
            'city'    => $city,
        ] = $this->weatherService->getDefaults();

        [$cityId, $lat, $lon] = $this->resolveLocation(
            null,
            $city,
            '',
            ''
        );

        try {
            $overview = $this->weatherService->fetchWeatherOverview(
                $lat,
                $lon,
                $cityId
            );

            return response()->json($overview);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Authenticated overview: lat/lon/city/country from request or defaults.
     */
    public function getWeatherOverview(WeatherOverviewRequest $request): JsonResponse
    {
        [$cityId, $lat, $lon] = $this->resolveLocation(
            $request->input('city_id'),
            $request->input('city'),
            $request->input('lat'),
            $request->input('lon')
        );

        $overview = $this->weatherService->fetchWeatherOverview(
            $lat,
            $lon,
            $request->input('city'),
            $cityId
        );

        return response()->json($overview);
    }

    /**
     * Resolve input to a valid City and coordinates.
     *
     * @param  int|null             $cityId
     * @param  string|null          $cityName
     * @param  float|string|null    $lat
     * @param  float|string|null    $lon
     * @return array [city_id, lat, lon]
     */

    protected function resolveLocation(
        ?int              $cityId,
        ?string           $cityName,
        float|string|null $lat,
        float|string|null $lon
    ): array {
        // Cast + round to 6 decimals for DECIMAL(10,6)
        $lat = $lat !== null ? round((float) $lat, 6) : null;
        $lon = $lon !== null ? round((float) $lon, 6) : null;
        // 1) Lookup by city_id
        if ($cityId) {
            try {
                $city = City::findOrFail($cityId);
            } catch (NotFoundException $e) {
                abort(404, "City with ID {$cityId} not found.");
            }
        }
        // 2) Lookup by cityName
        elseif ($cityName) {
            try {
                $city = City::where('name', $cityName)->firstOrFail();
            } catch (NotFoundException $e) {
                abort(404, "City named \"{$cityName}\" not found.");
            }
        }
        // 3) Lookup by coords (exact, then nearest, then create)
        elseif ($lat !== null && $lon !== null) {
            $city = City::where('lat', $lat)
                ->where('lon', $lon)
                ->first();

            if (! $city) {
                // nearest neighbor
                $city = City::orderByRaw(
                    '(ABS(lat - ?) + ABS(lon - ?)) ASC',
                    [$lat, $lon]
                )
                    ->first();
            }

            if (! $city) {
                // no cities at all? create placeholder
                $city = City::create([
                    'lat'     => $lat,
                    'lon'     => $lon,
                    'name'    => 'Custom',
                    'country' => '',
                ]);
            }
        }
        // 4) none provided
        else {
            abort(400, 'Must provide city_id, city name, or lat and lon.');
        }

        return [
            $city->id,
            (float) $city->lat,
            (float) $city->lon,
        ];
    }


}
