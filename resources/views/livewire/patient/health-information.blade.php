<div class="grid gap-4">
    <x-page-header
        title="My Health Information"
        subtitle="Everything recorded for you at {{ config('healthtrack.centre.name') }}."
    >
        <x-slot:aside>
            <span class="ht-pill">Read only</span>
        </x-slot:aside>
    </x-page-header>

    {{-- Appointments ------------------------------------------------------ --}}
    <div class="ht-panel">
        <div class="mb-3 flex items-center justify-between gap-2">
            <h2>Appointments</h2>
            <span class="ht-pill">{{ $appointments->count() }} total</span>
        </div>

        @if ($appointments->isEmpty())
            <div class="ht-empty">No appointments recorded.</div>
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
                        @foreach ($appointments as $appointment)
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

    {{-- One read-only panel per clinical record type. Adding a type to
         config/healthtrack.php makes it appear here automatically. --}}
    @foreach ($recordTypes as $key => $definition)
        <livewire:shared.clinical-records
            :patient="$patient"
            :type="$key"
            :read-only="true"
            :key="'portal-'.$key"
        />
    @endforeach
</div>
