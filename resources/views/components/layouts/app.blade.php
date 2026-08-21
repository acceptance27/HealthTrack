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
                       @if(request()->routeIs('health-worker.register-patient')) aria-current="page" @endif>
                        Register Patient
                    </a>
                @else
                    <a href="{{ route('patient.dashboard') }}"
                       @if(request()->routeIs('patient.dashboard')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3.75A.75.75 0 0 1 3.75 3h16.5A.75.75 0 0 1 21 3.75v16.5a.75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 20.25zm2.25-.75v5.25h5.25V3zm7.5 0v5.25h5.25V3zm-7.5 7.5v5.25h5.25v-5.25zm7.5 0v5.25h5.25v-5.25z"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('patient.my-health-information') }}"
                       @if(request()->routeIs('patient.my-health-information')) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3.75A1.75 1.75 0 0 0 5.25 5.5v13A1.75 1.75 0 0 0 7 20.25h10A1.75 1.75 0 0 0 18.75 18.5v-10l-4.25-4.75H7zm7.75 1.56L17.69 7.5h-2.94A.75.75 0 0 1 14 6.75v-1.44zM8.5 10.5h7M8.5 13.5h7M8.5 16.5h4.5"/></svg>
                        My Health Information
                    </a>
                @endif
            </nav>

            <div class="ht-user-wrap">
                <div class="ht-user-meta">
                    <div class="ht-user-name">{{ $user->name }}</div>
                    <div class="ht-user-role">{{ $user->role->label() }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="ht-header-logout">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 7V5.75A1.75 1.75 0 0 1 10.75 4h7.5A1.75 1.75 0 0 1 20 5.75v12.5A1.75 1.75 0 0 1 18.25 20h-7.5A1.75 1.75 0 0 1 9 18.25V17M3 12h10.5m0 0-3.5-3.5M13.5 12l-3.5 3.5"/></svg>
                        Log out
                    </button>
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
