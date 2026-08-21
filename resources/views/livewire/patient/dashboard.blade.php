<div class="grid gap-4">
    <x-page-header
        title="Hello, {{ $patient->first_name }}"
        subtitle="Your records at {{ config('healthtrack.centre.name') }}."
    >
        <x-slot:aside>
            <span class="ht-pill">Patient Portal</span>
        </x-slot:aside>
    </x-page-header>

    <div class="ht-metric-grid">
        <div class="ht-metric">
            <h3>Upcoming appointments</h3>
            <p style="color: var(--color-brand);">{{ $upcomingCount }}</p>
        </div>
        <div class="ht-metric">
            <h3>Recorded diagnoses</h3>
            <p>{{ $diagnosisCount }}</p>
        </div>
        <div class="ht-metric">
            <h3>Known allergies</h3>
            <p style="color: var(--color-brand-warm);">{{ $allergyCount }}</p>
        </div>
    </div>

    <div class="ht-panel">
        <h2>Your next appointments</h2>

        @if ($upcomingAppointments->isEmpty())
            <div class="ht-empty">
                You have no upcoming appointments.
                <span class="mt-1 block text-xs">
                    Contact the health centre to book one.
                </span>
            </div>
        @else
            <div class="ht-table-scroll">
                <table class="ht-table">
                    <thead>
                        <tr>
                            <th>Date and time</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($upcomingAppointments as $appointment)
                            <tr>
                                <td class="whitespace-nowrap font-bold"
                                    style="color: var(--color-brand-strong);">
                                    {{ $appointment->scheduled_at->format('M d, Y g:i A') }}
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
        <h2>Your health information</h2>
        <p class="ht-muted m-0 text-sm">
            Your diagnoses, lab results, doctor's notes, medical history and allergies
            are all recorded by the midwife and shown in one place.
        </p>
        <a href="{{ route('patient.my-health-information') }}" class="ht-button mt-3">
            View my health information
        </a>
    </div>
</div>
