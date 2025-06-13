@extends('layouts.app')
@section('content')
    @include('partials.city-search-form')
    <div class="dashboard-container">
        <div class="chart-card">
            @include('partials.current')
        </div>
        <div class="chart-card">
            @include('partials.forecast')
        </div>
        <div class="chart-card">
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        window.weatherResponse = @json($currentWeatherData);
        window.forecastResponse = @json($forecastWeatherData);
    </script>
    @vite('resources/js/index.js')
@endpush

