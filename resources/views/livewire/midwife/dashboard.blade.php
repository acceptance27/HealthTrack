<div class="grid gap-4">
    <x-page-header
        title="Midwife Dashboard"
        :subtitle="config('healthtrack.centre.name')"
    >
        <x-slot:aside>
            <span class="ht-pill">{{ now()->format('l, d F Y') }}</span>
        </x-slot:aside>
    </x-page-header>

    <div class="ht-metric-grid">
        <div class="ht-metric">
            <h3>Registered patients</h3>
            <p style="color: var(--color-brand);">{{ $patientCount }}</p>
        </div>
        <div class="ht-metric">
            <h3>Appointments today</h3>
            <p style="color: var(--color-brand-warm);">{{ $appointmentsToday }}</p>
        </div>
        <div class="ht-metric">
            <h3>Upcoming appointments</h3>
            <p>{{ $upcomingCount }}</p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-[2fr_1fr]">
        <div class="ht-panel">
            <h2>Today's schedule</h2>

            @if ($todaysAppointments->isEmpty())
                <div class="ht-empty">Nothing scheduled for today.</div>
            @else
                <div class="ht-table-scroll">
                    <table class="ht-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todaysAppointments as $appointment)
                                <tr>
                                    <td class="whitespace-nowrap font-bold">
                                        {{ $appointment->scheduled_at->format('g:i A') }}
                                    </td>
                                    <td>
                                        @if ($appointment->patient)
                                            <a href="{{ route('patients.show', $appointment->patient) }}"
                                               style="color: var(--color-brand-strong); font-weight: 700;">
                                                {{ $appointment->patient->fullName() }}
                                            </a>
                                        @else
                                            <span class="ht-muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td>{{ $appointment->reason }}</td>
                                    <td><span class="ht-pill">{{ ucfirst($appointment->status->value) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="ht-panel">
            <h2>Recently registered</h2>

            @if ($recentPatients->isEmpty())
                <div class="ht-empty">No patients yet.</div>
            @else
                <ul class="m-0 grid list-none gap-2 p-0">
                    @foreach ($recentPatients as $patient)
                        <li>
                            <a href="{{ route('patients.show', $patient) }}"
                               class="block rounded-xl p-3 text-sm"
                               style="background: var(--color-surface-muted); color: var(--color-ink);">
                                <span class="font-bold">{{ $patient->fullName() }}</span>
                                <span class="ht-muted block text-xs">
                                    Registered {{ $patient->created_at->diffForHumans() }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            <a href="{{ route('patients.index') }}" class="ht-button ht-button-muted mt-3">
                View all patients
            </a>
        </div>
    </div>
</div>
