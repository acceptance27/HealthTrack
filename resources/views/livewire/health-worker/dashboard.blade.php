<div class="grid gap-4">
    <x-page-header
        title="Health Worker Dashboard"
        subtitle="Register patients and keep their details up to date."
    >
        <x-slot:aside>
            <a href="{{ route('health-worker.register-patient') }}" class="ht-button">
                Register Patient
            </a>
        </x-slot:aside>
    </x-page-header>

    <div class="ht-metric-grid">
        <div class="ht-metric">
            <h3>Registered patients</h3>
            <p style="color: var(--color-brand);">{{ $patientCount }}</p>
        </div>
        <div class="ht-metric">
            <h3>Registered this month</h3>
            <p style="color: var(--color-brand-warm);">{{ $registeredThisMonth }}</p>
        </div>
        <div class="ht-metric">
            <h3>Without portal login</h3>
            <p>{{ $withoutPortalLogin }}</p>
        </div>
    </div>

    <div class="ht-panel">
        <h2>Recently registered</h2>

        @if ($recentPatients->isEmpty())
            <div class="ht-empty">No patients registered yet.</div>
        @else
            <div class="ht-table-scroll">
                <table class="ht-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Contact</th>
                            <th>Registered</th>
                            <th><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentPatients as $patient)
                            <tr>
                                <td class="font-bold" style="color: var(--color-brand-strong);">
                                    {{ $patient->fullName() }}
                                </td>
                                <td>{{ $patient->age() }}</td>
                                <td>{{ $patient->contact_number ?: '--' }}</td>
                                <td class="whitespace-nowrap">{{ $patient->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('patients.show', $patient) }}"
                                       class="ht-button ht-button-muted">Open record</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
