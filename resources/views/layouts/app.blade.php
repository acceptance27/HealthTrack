<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HealthTrack')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="app-body">
    @php
        $role = Auth::user()->role->value;
    @endphp

    <header class="topbar">
        <div class="topbar-inner">
            <a href="{{ route('dashboard') }}" class="brand-lockup">
                <span class="brand-mark">HT</span>
                <span>HealthTrack</span>
            </a>

            <nav class="topnav" aria-label="Primary navigation">
                @if($role === 'patient')
                    <a href="{{ route('patient.dashboard') }}" class="{{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('patient.my-health-information') }}" class="{{ request()->routeIs('patient.my-health-information') ? 'active' : '' }}">My Health Information</a>
                @elseif($role === 'midwife')
                    <a href="{{ route('midwife.dashboard') }}" class="{{ request()->routeIs('midwife.dashboard') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('midwife.patients') }}" class="{{ request()->routeIs('midwife.patients*') ? 'active' : '' }}">Patients</a>
                    <a href="{{ route('midwife.appointments') }}" class="{{ request()->routeIs('midwife.appointments*') ? 'active' : '' }}">Appointments</a>
                    <a href="{{ route('midwife.inventory') }}" class="{{ request()->routeIs('midwife.inventory*') ? 'active' : '' }}">Inventory</a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                @endif
            </nav>

            <div class="topbar-actions">
                <div id="real-time-clock" style="font-size: 11px; opacity: 0.7; color: var(--ink); margin-right: 15px; font-family: monospace;">{{ now()->format('d/F/Y H:i:s') }}</div>
                <div class="user-chip">
                    <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    <div>
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">{{ ucfirst($role) }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="button button-muted">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="app-content">
        <div class="page-heading">
            <h1>@yield('page-title', 'Dashboard')</h1>
        </div>

        @yield('content')
    </main>
    <script>
        function updateClock() {
            const now = new Date();
            const day = String(now.getDate()).padStart(2, '0');
            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            const month = monthNames[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const clockElement = document.getElementById('real-time-clock');
            if (clockElement) {
                clockElement.innerHTML = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    @livewireScripts
</body>
</html>
