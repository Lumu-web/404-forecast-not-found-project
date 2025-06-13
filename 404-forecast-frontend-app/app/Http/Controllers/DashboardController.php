<?php

namespace App\Http\Controllers;

use App\Services\ForecastService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Mix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DashboardController extends Controller
{
    public function __construct(
        protected ForecastService $forecastService
    ) {
        if (!$this->forecastService) {
            throw new \Exception('Forecast service is not available');
        }
    }
    private function getDefaultWeatherData(): array
    {
        $weatherOverviewData = $this->forecastService->fetchWeatherOverviewData();

        return [$weatherOverviewData['current'] ?? [], $weatherOverviewData['forecast'] ?? []];
    }

    public function dashboard(): mixed
    {
        try {
            $data = $this->getDefaultWeatherData();;
            [$currentWeatherData, $forecastWeatherData] = $data;
            $user = session('user');
            return view('index', compact('currentWeatherData', 'forecastWeatherData', 'user'));
        } catch (HttpException $e) {
            if ($e->getStatusCode() === 401) {
                return redirect('/login')->withErrors('Please login to access weather data.');
            }
            // Handle other errors (500 etc)
            return response()->view('errors.custom', ['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Generic error handling fallback
            return response()->view('errors.custom', ['message' => 'An unexpected error occurred.'], 500);
        }
    }

    public function current(): View
    {
        $currentWeatherData = $this->forecastService->fetchCurrentWeatherData();
        return view('current', compact('currentWeatherData'));
    }

    public function forecast(): View
    {
        $forecastWeatherData = $this->forecastService->fetchWeatherForecastData();
        return view('forecast', compact('forecastWeatherData'));
    }

    public function locations(Request $request): JsonResponse
    {
        $city = $request->input('city');

        if (!$city) {
            return response()->json(['error' => 'City is required'], 422);
        }

        $cityLocationsData = $this->forecastService->fetchCityLocationsData($city);

        return response()->json([$cityLocationsData]);
    }

    public function cityCharts(Request $request): JsonResponse
    {
        $input = $request->all();
        $lat = $input['lat'] ?? null;
        $lon = $input['lon'] ?? null;
        if (!$lat || !$lon) {
            return response()->json(['error' => 'Lat/Lon is required'], 422);
        }

        $weatherOverviewData = $this->forecastService->fetchWeatherOverviewData($lat, $lon);
        return response()->json([
            'current' => $weatherOverviewData['current'],
            'forecast' => $weatherOverviewData['forecast'],
        ]);
    }


    public function historical(): View
    {
        $weatherData = $this->forecastService->fetchCurrentWeatherData();
        return view('index', compact('weatherData'));
    }
}
