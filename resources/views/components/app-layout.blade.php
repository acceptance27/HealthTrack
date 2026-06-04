<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HealthTrack') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen">
        <nav class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold">HealthTrack</a>
                <div class="flex items-center gap-4 text-sm">
                    @auth
                        <span>{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-slate-600 hover:text-slate-950">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>
        <main class="mx-auto max-w-7xl px-4 py-6">{{ $slot }}</main>
    </div>
    @livewireScripts
</body>
</html>
