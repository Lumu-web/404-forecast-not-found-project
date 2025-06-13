@extends('layouts.app')

@section('content')
    @include('partials.forecast')
@endsection

@push('scripts')
    <script>
        window.forecastResponse = @json($forecastWeatherData); // data for forecast
    </script>
@endpush
