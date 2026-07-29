{{--
    The signed-in application shell.

    Every full-page Livewire component renders into this via the #[Layout]
    attribute on its class, e.g.:

        #[Layout('layouts.app')]
        class Dashboard extends Component { ... }

    The navigation is built from the user's role, so a new page only needs a
    line added to the matching @if branch below.
--}}
@php
    $user = auth()->user();
    $centre = config('healthtrack.centre');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'HealthTrack' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="ht-topbar">
        <div class="ht-topbar-inner">
            <a href="{{ route('dashboard') }}" class="ht-brand">
                <span class="ht-brand-mark">HT</span>
                <span>HealthTrack</span>
            </a>

            <nav class="ht-nav" aria-label="Primary">
                @if ($user->isMidwife())
                    <a href="{{ route('midwife.dashboard') }}"
                       @if(request()->routeIs('midwife.dashboard')) aria-current="page" @endif>Dashboard</a>
                    <a href="{{ route('patients.index') }}"
                       @if(request()->routeIs('patients.*')) aria-current="page" @endif>Patients</a>
                    <a href="{{ route('midwife.appointments') }}"
                       @if(request()->routeIs('midwife.appointments')) aria-current="page" @endif>Appointments</a>
                @elseif ($user->isHealthWorker())
                    <a href="{{ route('health-worker.dashboard') }}"
                       @if(request()->routeIs('health-worker.dashboard')) aria-current="page" @endif>Dashboard</a>
                    <a href="{{ route('patients.index') }}"
                       @if(request()->routeIs('patients.*')) aria-current="page" @endif>Patients</a>
                    <a href="{{ route('health-worker.register-patient') }}"
                       @if(request()->routeIs('health-worker.register-patient')) aria-current="page" @endif>Register Patient</a>
                @else
                    <a href="{{ route('patient.dashboard') }}"
                       @if(request()->routeIs('patient.dashboard')) aria-current="page" @endif>Dashboard</a>
                    <a href="{{ route('patient.my-health-information') }}"
                       @if(request()->routeIs('patient.my-health-information')) aria-current="page" @endif>My Health Information</a>
                @endif
            </nav>

            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-sm font-bold leading-tight">{{ $user->name }}</div>
                    <div class="ht-muted text-xs">{{ $user->role->label() }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="ht-button ht-button-muted">Log out</button>
                </form>
            </div>
        </div>
    </header>

    <main class="ht-content">
        <x-flash />
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
