@extends('layouts.app')

@section('title', 'Appointments')
@section('page-title', '')

@section('content')
<div class="page-heading flex items-center justify-between">
    <div class="flex items-center gap-3">
        <h1 class="m-0">Barangay Appointments</h1>
        <span class="mw-pill">{{ $appointments->total() }} Total</span>
    </div>
</div>

<section class="card">
    <div class="overflow-x-auto">
        <table class="mw-status-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($appointments as $appointment)
                    <tr onclick="window.location='{{ route('midwife.patients.show', $appointment->patient_id) }}?section=appointments'" class="cursor-pointer">
                        <td>{{ $appointment->scheduled_at->format('M d, Y g:i A') }}</td>
                        <td class="font-bold text-[var(--ev-accent-strong)]">{{ $appointment->patient?->name ?? 'Unknown patient' }}</td>
                        <td>{{ $appointment->reason }}</td>
                        <td>
                            <span class="mw-pill" style="background: {{ match($appointment->status->value ?? $appointment->status) {
                                'scheduled' => 'rgba(59, 130, 246, 0.1)',
                                'completed' => 'rgba(16, 185, 129, 0.1)',
                                'cancelled' => 'rgba(239, 68, 68, 0.1)',
                                default => 'var(--surface-muted)'
                            } }}; color: {{ match($appointment->status->value ?? $appointment->status) {
                                'scheduled' => '#3b82f6',
                                'completed' => '#10b981',
                                'cancelled' => '#ef4444',
                                default => 'var(--ink-soft)'
                            } }}">
                                {{ ucfirst($appointment->status->value ?? $appointment->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center mw-muted">No appointments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $appointments->links() }}
    </div>
</section>
@endsection
