<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Frontend') }}</title>

    @vite(['resources/js/app.js'])

    @stack('head')
</head>
<body style="position: relative">
<div class="min-h-screen bg-gray-100">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Page Content -->
    @yield('content')
</div>
@stack('scripts')
@if(isset($isGuest))
    @include('partials.auth.register')
    @include('partials.auth.login')
@endif


</body>
</html>
