<div class="grid gap-4">
    <x-page-header
        title="Appointments"
        subtitle="Schedule new appointments from a patient's record."
    >
        <x-slot:aside>
            <span class="ht-pill">{{ $appointments->total() }} shown</span>
        </x-slot:aside>
    </x-page-header>

    <div class="ht-panel">
        <label class="ht-field mb-3 max-w-xs">
            <span>Show</span>
            <select wire:model.live="filter" class="ht-input">
                <option value="upcoming">Upcoming</option>
                <option value="today">Today</option>
                <option value="past">Past</option>
                <option value="all">All</option>
            </select>
        </label>

        @if ($appointments->isEmpty())
            <div class="ht-empty">No appointments in this view.</div>
        @else
            <div class="ht-table-scroll">
                <table class="ht-table">
                    <thead>
                        <tr>
                            <th>Date and time</th>
                            <th>Patient</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointments as $appointment)
                            <tr wire:key="appointment-{{ $appointment->id }}">
                                <td class="whitespace-nowrap font-bold"
                                    style="color: var(--color-brand-strong);">
                                    {{ $appointment->scheduled_at->format('M d, Y g:i A') }}
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
                                <td>
                                    {{-- Changing the select saves immediately. --}}
                                    <select
                                        class="ht-input"
                                        style="min-width: 130px;"
                                        wire:change="setStatus({{ $appointment->id }}, $event.target.value)"
                                    >
                                        @foreach ($statuses as $case)
                                            <option value="{{ $case->value }}"
                                                @selected($appointment->status === $case)>
                                                {{ ucfirst($case->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>
