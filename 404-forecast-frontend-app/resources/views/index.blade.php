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
    </div>
    <div class="chart-card">
        @include('partials.map')
    </div>
@endsection
@push('scripts')
    <script>
        window.weatherResponse = @json($currentWeatherData);
        window.forecastResponse = @json($forecastWeatherData);
    </script>
@endpush
