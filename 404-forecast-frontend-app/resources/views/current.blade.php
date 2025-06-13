@extends('layouts.app')

@section('content')
    @include('partials.current')
@endsection

@push('scripts')
    <script>
        window.weatherResponse = @json($currentWeatherData);
    </script>
@endpush
