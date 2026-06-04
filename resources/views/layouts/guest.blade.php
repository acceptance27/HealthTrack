<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'HealthTrack') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-md items-center px-4">
        <div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </main>
</body>
</html>
