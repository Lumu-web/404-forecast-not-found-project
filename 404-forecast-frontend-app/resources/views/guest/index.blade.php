@extends('layouts.app')
@section('content')
    <div class="dashboard-container">
        <div class="chart-card">
            @include('partials.graphs.current-mood-bar')
        </div>
    </div>
    @include('partials._guest_cta')
@endsection
@push('scripts')
    <script>
        window.currentMoodBarResponse = @json($currentMoodBarData);
    </script>
@endpush
