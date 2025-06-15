@extends('layouts.app')

@section('content')
    @include('partials.current')
@endsection

@push('scripts')
    <script>
        window.currentMoodBarResponse = @json($currentMoodBarData);
    </script>
@endpush
