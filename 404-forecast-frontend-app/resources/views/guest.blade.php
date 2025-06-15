@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
        {{-- Current Conditions Mood Chart for Guests --}}
        <div class="chart-card">
            @include('dashboard.current-mood')
        </div>

        {{-- Guest call-to-action (login/register) --}}
        <div class="chart-card">
            @include('partials._guest_cta')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.currentMoodData = @json($currentMoodBarData);
    </script>
@endpush
