@extends('layouts.app')

@section('title', $patient->fullName())
@section('page-title', 'Patient Record')

@section('content')
@php
    $sections = [
        'general' => 'General',
        'diagnoses' => 'Diagnoses',
        'doctor-notes' => 'Doctor Notes',
        'lab-values' => 'Lab Values',
        'medical-history' => 'Medical History',
    ];
@endphp

<style>
    .patient-record-layout {
        --ev-bg: #f6f1e8;
        --ev-bg-deep: #e8e0d1;
        --ev-surface: #ffffff;
        --ev-surface-muted: #f2ede4;
        --ev-ink: #1f2421;
        --ev-ink-soft: #3f473f;
        --ev-accent: #0f6b5f;
        --ev-accent-strong: #0c4f46;
        --ev-accent-warm: #d0742a;
        --ev-border: rgba(31, 36, 33, 0.12);
        --ev-shadow: 0 12px 30px rgba(15, 20, 16, 0.08);

        display: grid;
        grid-template-columns: 200px 1fr;
        grid-auto-rows: min-content;
        gap: 16px;
        margin: -8px;
        padding: 16px;
        border-radius: 20px;
        color: var(--ev-ink);
        font-family: "Trebuchet MS", "Gill Sans", "Candara", sans-serif;
        background:
            radial-gradient(circle at top right, #f3dcc8, transparent 44%),
            radial-gradient(circle at 12% 18%, rgba(15, 107, 95, 0.12), transparent 36%),
            linear-gradient(140deg, var(--ev-bg) 0%, var(--ev-bg-deep) 100%);
    }

    .patient-record-layout * {
        box-sizing: border-box;
    }

    .mw-card {
        background: var(--ev-surface);
        border: 1px solid var(--ev-border);
        border-radius: 16px;
        box-shadow: var(--ev-shadow);
    }

    .patient-record-nav {
        position: sticky;
        top: 22px;
        align-self: start;
        display: grid;
        gap: 8px;
        padding: 18px;
        z-index: 10;
    }

    .patient-record-nav a {
        display: flex;
        align-items: center;
        min-height: 36px;
        padding: 8px 12px;
        border-radius: 10px;
        color: var(--ev-ink);
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .patient-record-nav a:hover,
    .patient-record-nav a.active {
        background: var(--ev-surface-muted);
        color: var(--ev-accent-strong);
    }

    .patient-record-nav .back-link {
        margin-bottom: 6px;
        color: var(--ev-accent-strong);
    }

    .patient-main {
        display: grid;
        gap: 16px;
        min-width: 0;
    }

    .mw-page-header {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        padding: 18px;
    }

    .mw-page-header h2 {
        margin: 0 0 4px;
        font-family: "Palatino Linotype", "Book Antiqua", serif;
        font-size: 24px;
        line-height: 1.1;
        color: var(--ev-ink);
    }

    .mw-page-header p,
    .mw-muted {
        color: var(--ev-ink-soft);
    }

    .mw-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: fit-content;
        padding: 6px 12px;
        border: 1px solid var(--ev-border);
        border-radius: 999px;
        background: var(--ev-surface);
        color: var(--ev-accent-strong);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .mw-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .mw-panel {
        padding: 16px;
    }
    
    .mw-panel h3 {
        margin: 0 0 10px;
        color: var(--ev-ink);
        font-size: 16px;
        font-weight: 800;
    }

    .mw-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--ev-border);
        border-radius: 10px;
        background: var(--ev-surface);
        color: var(--ev-ink);
        font-size: 13px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .mw-input:focus {
        outline: none;
        border-color: var(--ev-accent);
        box-shadow: 0 0 0 3px rgba(15, 107, 95, 0.1);
    }

    .mw-status-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
        background: var(--ev-surface);
        border: 1px solid var(--ev-border);
        border-radius: 12px;
        overflow: hidden;
    }
    
    .mw-status-table th,
    .mw-status-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--ev-border);
        border-right: 1px solid var(--ev-border);
        text-align: left;
    }

    .mw-status-table th:last-child,
    .mw-status-table td:last-child {
        border-right: none;
    }

    .mw-status-table tr:last-child td {
        border-bottom: none;
    }

    .mw-status-table th {
        background: var(--ev-surface-muted);
        color: var(--ev-ink-soft);
        font-weight: 800;
    }

    .mw-status-table tbody tr:nth-child(even) {
        background: rgba(242, 237, 228, 0.4);
    }

    .mw-status-table tbody tr:hover {
        background: rgba(15, 107, 95, 0.04);
    }

    .mw-empty-state {
        display: grid;
        place-content: center;
        min-height: 80px;
        border: 1px dashed var(--ev-border);
        border-radius: 14px;
        background: var(--ev-surface-muted);
        color: var(--ev-ink-soft);
        font-size: 13px;
        text-align: center;
    }

    @media (max-width: 1100px) {
        .mw-two-col {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 980px) {
        .patient-record-layout {
            grid-template-columns: 1fr;
            padding: 16px;
        }

        .patient-record-nav {
            position: static;
            display: flex;
            overflow-x: auto;
            padding: 10px;
        }

        .patient-record-nav a {
            white-space: nowrap;
        }
    }

    @media (max-width: 720px) {
        .mw-page-header {
            flex-direction: column;
        }
    }
</style>

<div class="patient-record-layout">
    <aside class="mw-card patient-record-nav" aria-label="Patient record sections">
        <a href="{{ route('midwife.patients') }}" class="back-link">
            &lt;- Patients
        </a>

        @foreach ($sections as $key => $label)
            <a
                href="{{ route('midwife.patients.show', ['id' => $patient->id, 'section' => $key]) }}"
                class="{{ $section === $key ? 'active' : '' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </aside>

    <main class="patient-main">
        <section class="mw-card mw-page-header">
            <div>
                <h2>{{ $patient->fullName() }}</h2>
                <p>
                    {{ ucfirst($patient->sex) }} | Born {{ $patient->birthdate->format('M d, Y') }} | {{ $patient->address }}
                </p>
                <p class="mt-1">Contact: {{ $patient->contact_number ?? 'N/A' }}</p>
            </div>
            <div class="flex flex-col items-end gap-2">
                <span class="mw-pill">{{ $sections[$section] }}</span>
                @if (session('status'))
                    <div class="mw-pill" style="color: var(--ev-accent);">
                        {{ session('status') }}
                    </div>
                @endif
            </div>
        </section>

        @if ($section === 'general')
            <div class="mw-two-col">
                <section class="mw-card mw-panel">
                    <x-summary-panel 
                        title="Allergies" 
                        :records="$allergies" 
                        primary="allergen" 
                        date="recorded_at" 
                        secondary="reaction"
                        label="Allergy"
                    />
                </section>

                <section class="mw-card mw-panel" style="align-self: start;">
                    <h3>Add Appointment</h3>
                    <form method="POST" action="{{ route('midwife.patients.appointments.store', $patient) }}" class="grid gap-2">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <label class="grid gap-1 text-sm font-bold mw-muted">
                                Date and Time
                                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="mw-input" required>
                                @error('scheduled_at') <span class="text-xs text-red-700">{{ $message }}</span> @enderror
                            </label>

                            <label class="grid gap-1 text-sm font-bold mw-muted">
                                Status
                                <select name="status" class="mw-input" required>
                                    @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status)
                                        <option value="{{ $status }}" @selected(old('status', 'pending') === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                @error('status') <span class="text-xs text-red-700">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <label class="grid gap-1 text-sm font-bold mw-muted">
                            Reason
                            <input type="text" name="reason" value="{{ old('reason') }}" class="mw-input" placeholder="e.g. Monthly Checkup" required>
                            @error('reason') <span class="text-xs text-red-700">{{ $message }}</span> @enderror
                        </label>

                        <label class="grid gap-1 text-sm font-bold mw-muted">
                            Notes
                            <textarea name="notes" rows="2" class="mw-input" placeholder="Additional details...">{{ old('notes') }}</textarea>
                            @error('notes') <span class="text-xs text-red-700">{{ $message }}</span> @enderror
                        </label>

                        <button type="submit" class="mw-pill" style="width: 100%; background: var(--ev-accent-strong); color: white; border: none; padding: 8px;">Add Appointment</button>
                    </form>
                </section>
            </div>

            <section class="mw-card mw-panel">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="m-0">Appointments History</h3>
                    <span class="mw-pill">{{ $appointments->count() }} Total</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="mw-status-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($appointments as $appointment)
                                    <tr>
                                        <td class="whitespace-nowrap">{{ $appointment->scheduled_at->format('M d, Y g:i A') }}</td>
                                        <td class="font-bold text-[var(--ev-accent-strong)]">{{ $appointment->reason }}</td>
                                        <td>
                                            <span class="mw-pill" style="padding: 2px 8px; font-size: 10px;">
                                                {{ ucfirst($appointment->status->value ?? $appointment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('midwife.patients.appointments.destroy', ['id' => $patient->id, 'appointment' => $appointment->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-bold">Remove</button>
                                            </form>
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
            </section>
        @elseif ($section === 'diagnoses')
            <div class="mw-card mw-panel">
                <x-summary-panel 
                    title="Diagnoses" 
                    :records="$diagnoses" 
                    primary="diagnosis" 
                    date="diagnosed_at" 
                    secondary="description"
                    label="Diagnosis"
                />
            </div>
        @elseif ($section === 'doctor-notes')
            <div class="mw-card mw-panel">
                <x-summary-panel 
                    title="Doctor Notes" 
                    :records="$doctorNotes" 
                    primary="title" 
                    date="noted_at" 
                    secondary="content"
                    label="Note"
                />
            </div>
        @elseif ($section === 'lab-values')
            <div class="mw-card mw-panel">
                <x-summary-panel 
                    title="Lab Values" 
                    :records="$labValues" 
                    primary="test_name" 
                    date="tested_at" 
                    value="value"
                    unit="unit"
                    range="reference_range"
                    label="Test Name"
                />
            </div>
        @elseif ($section === 'medical-history')
            <div class="mw-card mw-panel">
                <x-summary-panel 
                    title="Medical History" 
                    :records="$medicalHistories" 
                    primary="condition" 
                    date="recorded_at" 
                    secondary="notes"
                    label="Condition"
                />
            </div>
        @endif
    </main>
</div>
@endsection
