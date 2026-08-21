@php
    $patientName = trim(($patient->first_name ?? '') . ' ' . ($patient->middle_name ?? '') . ' ' . ($patient->last_name ?? '')) ?: 'Patient';
    $patientAge = $patient->birthdate ? $patient->age() : null;
    $vitalSigns = $patient->labValues()->orderByDesc('tested_at')->get();
@endphp

<div class="patient-healthinfo-page">
    <aside class="patient-healthinfo-sidebar">
        <nav class="patient-healthinfo-nav" aria-label="Health information navigation">
            <a href="{{ route('patient.dashboard') }}" class="patient-sidebar-item">
                <span class="patient-sidebar-icon">
                    <svg viewBox="0 0 24 24"><path d="M4 10.5 12 3l8 7.5V19a1 1 0 0 1-1 1h-4v-7H9v7H5a1 1 0 0 1-1-1v-8.5z"/></svg>
                </span>
                <span>Overview</span>
            </a>

            <div class="patient-sidebar-label">MY HEALTH INFORMATION</div>

            <a href="#appointments" class="patient-sidebar-item is-active">
                <span class="patient-sidebar-icon">
                    <svg viewBox="0 0 24 24"><path d="M7 3.5v2.5M17 3.5v2.5M4.5 9.5h15M6.5 5.5h11A2 2 0 0 1 19.5 7.5v9A2 2 0 0 1 17.5 18.5h-11A2 2 0 0 1 4.5 16.5v-9A2 2 0 0 1 6.5 5.5z"/></svg>
                </span>
                <span>Appointments</span>
            </a>

            <a href="#patient-information" class="patient-sidebar-item">
                <span class="patient-sidebar-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm-7 8a7 7 0 0 1 14 0"/></svg>
                </span>
                <span>Patient Information</span>
            </a>

            <a href="#vital-signs" class="patient-sidebar-item">
                <span class="patient-sidebar-icon">
                    <svg viewBox="0 0 24 24"><path d="M3 12.5c1.5-4 4.5-6 8-6 3.5 0 6.5 2 8 6-1.5 4-4.5 6-8 6-3.5 0-6.5-2-8-6zm9-4.5v8m-3.5-3.5h7"/></svg>
                </span>
                <span>Vital Signs</span>
            </a>
        </nav>

        <div class="patient-help-card">
            <div class="patient-help-icon">
                <svg viewBox="0 0 24 24"><path d="M12 3.5 5.8 6.2v5.8c0 4.1 2.7 7.8 6.2 9.3 3.5-1.5 6.2-5.2 6.2-9.3V6.2L12 3.5zm-1.2 6.3h2.4v2.4h-2.4zm0 4h2.4v2.3h-2.4z"/></svg>
            </div>
            <h3>Need help?</h3>
            <p>Contact your midwife or health worker for assistance.</p>
        </div>
    </aside>

    <main class="patient-healthinfo-main">
        <section class="patient-healthinfo-header-card">
            <div class="patient-healthinfo-header-copy">
                <div class="patient-panel-icon patient-panel-icon-large">
                    <svg viewBox="0 0 24 24"><path d="M7 3.75A1.75 1.75 0 0 0 5.25 5.5v13A1.75 1.75 0 0 0 7 20.25h10A1.75 1.75 0 0 0 18.75 18.5v-10l-4.25-4.75H7zm7.75 1.56L17.69 7.5h-2.94A.75.75 0 0 1 14 6.75v-1.44zM8.5 10.5h7M8.5 13.5h7M8.5 16.5h4.5"/></svg>
                </div>
                <div>
                    <h1>My Health Information</h1>
                    <p>Everything has been recorded here at {{ config('healthtrack.centre.name') }}.</p>
                </div>
            </div>
            <span class="patient-readonly-pill">
                <svg viewBox="0 0 24 24"><path d="M7 10.5V8.7A5 5 0 0 1 12 4a5 5 0 0 1 5 4.7v1.8M7.5 10.5h9A1.5 1.5 0 0 1 18 12v6.5A1.5 1.5 0 0 1 16.5 20h-9A1.5 1.5 0 0 1 6 18.5V12a1.5 1.5 0 0 1 1.5-1.5z"/></svg>
                Read Only
            </span>
        </section>

        <section id="appointments" class="patient-healthinfo-card">
            <div class="patient-card-header">
                <div class="patient-card-title">
                    <span class="patient-panel-icon patient-panel-icon-small">
                        <svg viewBox="0 0 24 24"><path d="M7 3.5v2.5M17 3.5v2.5M4.5 9.5h15M6.5 5.5h11A2 2 0 0 1 19.5 7.5v9A2 2 0 0 1 17.5 18.5h-11A2 2 0 0 1 4.5 16.5v-9A2 2 0 0 1 6.5 5.5z"/></svg>
                    </span>
                    <h2>Appointments</h2>
                </div>
                <span class="patient-card-total">{{ $appointments->count() }} total</span>
            </div>

            @if ($appointments->isEmpty())
                <div class="patient-empty-state">No appointments recorded.</div>
            @else
                <div class="patient-table-wrap">
                    <table class="patient-health-table">
                        <thead>
                            <tr>
                                <th>Date and Time</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointments as $appointment)
                                <tr>
                                    <td class="patient-date-cell">{{ $appointment->scheduled_at->format('M d, Y g:i A') }}</td>
                                    <td>{{ $appointment->reason }}</td>
                                    <td><span class="patient-status-pill">{{ ucfirst($appointment->status->value) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="patient-table-footer">
                Showing 1 to {{ $appointments->count() }} of {{ $appointments->count() }} appointments
            </div>
        </section>

        <section id="patient-information" class="patient-healthinfo-card">
            <div class="patient-card-header">
                <div class="patient-card-title">
                    <span class="patient-panel-icon patient-panel-icon-small">
                        <svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm-7 8a7 7 0 0 1 14 0"/></svg>
                    </span>
                    <h2>Patient Information</h2>
                </div>
                <button type="button" class="patient-view-all-button">
                    <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6S2.5 12 2.5 12zm9.5 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                    View All
                </button>
            </div>

            <div class="patient-information-grid">
                <div class="patient-info-field">
                    <span class="patient-info-label">Full Name</span>
                    <span class="patient-info-value">{{ $patientName }}</span>
                </div>
                <div class="patient-info-field">
                    <span class="patient-info-label">Gender</span>
                    <span class="patient-info-value">{{ ucfirst($patient->sex ?? 'Not provided') }}</span>
                </div>
                <div class="patient-info-field">
                    <span class="patient-info-label">Date of Birth</span>
                    <span class="patient-info-value">{{ $patient->birthdate ? $patient->birthdate->format('M d, Y') : 'Not provided' }}</span>
                </div>
                <div class="patient-info-field">
                    <span class="patient-info-label">Age</span>
                    <span class="patient-info-value">{{ $patientAge !== null ? $patientAge . ' years old' : 'Not provided' }}</span>
                </div>

                <div class="patient-info-field">
                    <span class="patient-info-label">Contact Number</span>
                    <span class="patient-info-value">{{ $patient->contact_number ?: 'Not provided' }}</span>
                </div>
                <div class="patient-info-field patient-info-field-wide">
                    <span class="patient-info-label">Address</span>
                    <span class="patient-info-value">{{ $patient->address ?: 'Not provided' }}</span>
                </div>
                <div class="patient-info-field">
                    <span class="patient-info-label">Blood Type</span>
                    <span class="patient-info-value">{{ $patient->blood_type ?: 'Not provided' }}</span>
                </div>
            </div>
        </section>

        <section id="vital-signs" class="patient-healthinfo-card">
            <div class="patient-card-header">
                <div class="patient-card-title">
                    <span class="patient-panel-icon patient-panel-icon-small">
                        <svg viewBox="0 0 24 24"><path d="M3 12.5c1.5-4 4.5-6 8-6 3.5 0 6.5 2 8 6-1.5 4-4.5 6-8 6-3.5 0-6.5-2-8-6zm9-4.5v8m-3.5-3.5h7"/></svg>
                    </span>
                    <h2>Vital Signs</h2>
                </div>
                <button type="button" class="patient-view-all-button">
                    <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6S2.5 12 2.5 12zm9.5 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                    View All
                </button>
            </div>

            @if ($vitalSigns->isEmpty())
                <div class="patient-empty-state">No vital signs recorded.</div>
            @else
                <div class="patient-table-wrap">
                    <table class="patient-health-table">
                        <thead>
                            <tr>
                                <th>Measurement</th>
                                <th>Result</th>
                                <th>Unit</th>
                                <th>Date Recorded</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vitalSigns as $vitalSign)
                                <tr>
                                    <td>{{ $vitalSign->test_name }}</td>
                                    <td>{{ $vitalSign->value }}</td>
                                    <td>{{ $vitalSign->unit ?: '--' }}</td>
                                    <td>{{ $vitalSign->tested_at ? $vitalSign->tested_at->format('M d, Y') : '--' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="patient-table-footer">
                Showing latest vital signs
            </div>
        </section>

        <div class="patient-page-footer">
            <span class="patient-page-text">Page 1 of 2</span>
            <div class="patient-page-dots" aria-label="Pagination">
                <span class="patient-page-dot is-active"></span>
                <span class="patient-page-dot"></span>
            </div>
            <button type="button" class="patient-next-page-button">
                Next Page
                <svg viewBox="0 0 24 24"><path d="M5 12h12M13 5l7 7-7 7"/></svg>
            </button>
        </div>
    </main>
</div>
