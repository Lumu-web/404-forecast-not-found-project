@extends('layouts.app')

@section('content')
    @include('partials.city-search-form')

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 p-4">
        {{-- Current Conditions Mood Chart --}}
        <div class="chart-card">
            @include('dashboard.current-mood')
        </div>

        {{-- Next-24h “Feels Like” Trend --}}
        <div class="chart-card">
            @include('dashboard.feels-like')
        </div>

        {{-- Next-24h Precipitation Probability --}}
        <div class="chart-card">
            @include('dashboard.precipitation')
        </div>

        {{-- Map / Location Picker --}}
        <div class="chart-card">
            @include('dashboard.map')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Data from controller
        window.currentMoodData      = @json($currentMoodData);
        window.next24FeelsLikeTrend = @json($feelsLikeData);
        window.next24PopData        = @json($popData);
    </script>
@endpush
