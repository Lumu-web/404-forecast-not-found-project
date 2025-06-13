@extends('layouts.app')
@section('content')
    <div class="dashboard-container">
        <div class="chart-card">
            @include('partials.current')
        </div>
        <div class="chart-card">
            @include('partials.forecast')
        </div>
    </div>
    @include('partials._guest_cta')
@endsection
@push('scripts')
    <script>
        window.weatherResponse = @json($currentWeatherData);
        window.forecastResponse = @json($forecastWeatherData);
    </script>
@endpush
