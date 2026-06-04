<x-patient-page title="Patient Dashboard" description="Your personal health summary and upcoming appointment details.">
    <section class="mw-summary-grid">
        <article class="mw-card mw-metric">
            <h3>Upcoming Appointments</h3>
            <p>{{ $upcomingAppointments }}</p>
        </article>
        <article class="mw-card mw-metric">
            <h3>Diagnoses</h3>
            <p class="mw-green">{{ $diagnosesCount }}</p>
        </article>
        <article class="mw-card mw-metric">
            <h3>Doctor Notes</h3>
            <p class="mw-warm">{{ $notesCount }}</p>
        </article>
    </section>

    <section class="mw-card mw-panel">
        <h3>Personal Record</h3>
        <p class="mw-muted">Only your own patient information appears here. Your appointments, lab values, diagnoses, allergies, and doctor notes are scoped to your account.</p>
        <a href="{{ route('patient.my-health-information') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            View My Health Information
        </a>
    </section>
</x-patient-page>
