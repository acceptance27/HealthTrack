<x-patient-page title="Appointments" description="Manage your appointment requests and view upcoming visits.">
    <section class="mw-two-col">
        <article class="mw-card mw-panel">
            <h3>Request Appointment</h3>
            <livewire:patient.appointments.appointment-form />
        </article>

        <article class="mw-card mw-panel">
            <h3>Upcoming Appointments</h3>
            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                <table class="mw-status-table">
                    <thead>
                        <tr>
                            <th>Schedule</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->scheduled_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $appointment->reason }}</td>
                                <td>{{ ucfirst($appointment->status->value) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">No appointments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $appointments->links() }}
        </article>
    </section>
</x-patient-page>
