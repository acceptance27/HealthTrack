@php
    $patient = $this->patient;
@endphp

<div class="grid gap-4">
    <x-page-header
        :title="$patient->fullName()"
        :subtitle="ucfirst($patient->sex).' | '.$patient->age().' years old | Born '.$patient->birthdate->format('M d, Y')"
    >
        <x-slot:aside>
            <a href="{{ route('patients.index') }}" class="ht-button ht-button-muted">Back to patients</a>
        </x-slot:aside>
    </x-page-header>

    <div class="ht-record-layout">
        {{-- Section tabs. Built from config, so a new record type appears here
             automatically. --}}
        <nav class="ht-record-nav" aria-label="Record sections">
            @foreach ($sections as $key => $label)
                <a
                    href="#"
                    wire:click.prevent="$set('section', '{{ $key }}')"
                    @if($section === $key) aria-current="page" @endif
                >{{ $label }}</a>
            @endforeach
        </nav>

        <div class="grid gap-4">
            @if ($section === 'general')

                {{-- Demographics ------------------------------------------ --}}
                <div class="ht-panel">
                    <h2>Patient details</h2>
                    <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                        @foreach ([
                            'Address' => $patient->address,
                            'Contact number' => $patient->contact_number,
                            'PhilHealth number' => $patient->philhealth_number,
                            'Emergency contact' => $patient->emergency_contact_name,
                            'Emergency number' => $patient->emergency_contact_number,
                        ] as $label => $value)
                            <div>
                                <dt class="ht-muted text-xs font-bold">{{ $label }}</dt>
                                <dd class="m-0">{{ $value ?: '--' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                {{-- Portal account ---------------------------------------- --}}
                {{-- Creating patient accounts is the midwife's job, not the
                     health worker's, per the study's Level 1 DFD. --}}
                <div class="ht-panel">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <h2>Portal account</h2>
                        @if (! $patient->user_id && $this->canCreateAccount)
                            <button type="button" wire:click="toggleAccountForm" class="ht-button">
                                {{ $showAccountForm ? 'Cancel' : 'Create account' }}
                            </button>
                        @endif
                    </div>

                    @if ($patient->user_id)
                        <p class="m-0 text-sm">
                            Signs in as <strong>{{ $patient->user->email }}</strong>
                        </p>
                        <p class="ht-muted m-0 mt-1 text-xs">
                            The patient sets their own password with "Forgot password".
                            Staff never see it.
                        </p>
                    @elseif ($showAccountForm && $this->canCreateAccount)
                        <form wire:submit="createPortalAccount"
                              class="grid gap-3 rounded-xl p-4"
                              style="background: var(--color-surface-muted);">
                            <p class="ht-muted m-0 text-xs">
                                Creates a login so this patient can view their own records.
                                No password is set here — they choose one themselves using
                                the "Forgot password" link.
                            </p>
                            <div class="max-w-md">
                                <x-field name="portalEmail" label="Email address"
                                         type="email" wire="portalEmail" :required="true" />
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="ht-button" wire:loading.attr="disabled">
                                    Create account
                                </button>
                                <button type="button" wire:click="toggleAccountForm"
                                        class="ht-button ht-button-muted">Cancel</button>
                            </div>
                        </form>
                    @else
                        <div class="ht-empty">
                            No portal account.
                            @if (! $this->canCreateAccount)
                                Only the midwife can create one.
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Appointments ------------------------------------------ --}}
                <div class="ht-panel">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <h2>Appointments</h2>
                        <div class="flex items-center gap-2">
                            <span class="ht-pill">{{ $appointments->count() }} total</span>
                            @if ($this->canSchedule)
                                <button type="button" wire:click="toggleAppointmentForm" class="ht-button">
                                    {{ $showAppointmentForm ? 'Cancel' : 'Schedule appointment' }}
                                </button>
                            @endif
                        </div>
                    </div>

                    @if ($showAppointmentForm && $this->canSchedule)
                        <form wire:submit="scheduleAppointment"
                              class="mb-4 grid gap-3 rounded-xl p-4"
                              style="background: var(--color-surface-muted);">

                            <div class="grid gap-3 sm:grid-cols-2">
                                <x-field name="scheduledAt" label="Date and time"
                                         type="datetime-local" wire="scheduledAt" :required="true" />
                                <label class="ht-field">
                                    <span>Status</span>
                                    <select wire:model="status" class="ht-input">
                                        @foreach ($statuses as $case)
                                            <option value="{{ $case->value }}">{{ ucfirst($case->value) }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <span class="ht-error">{{ $message }}</span> @enderror
                                </label>
                            </div>

                            <x-field name="reason" label="Reason" wire="reason"
                                     placeholder="e.g. Prenatal check-up" :required="true" />
                            <x-field name="notes" label="Notes" type="textarea" wire="notes" />

                            <div class="flex gap-2">
                                <button type="submit" class="ht-button" wire:loading.attr="disabled">
                                    Save appointment
                                </button>
                                <button type="button" wire:click="toggleAppointmentForm"
                                        class="ht-button ht-button-muted">Cancel</button>
                            </div>
                        </form>
                    @endif

                    @if ($appointments->isEmpty())
                        <div class="ht-empty">No appointments for this patient.</div>
                    @else
                        <div class="ht-table-scroll">
                            <table class="ht-table">
                                <thead>
                                    <tr>
                                        <th>Date and time</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        @if ($this->canSchedule)
                                            <th><span class="sr-only">Actions</span></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($appointments as $appointment)
                                        <tr wire:key="appointment-{{ $appointment->id }}">
                                            <td class="whitespace-nowrap font-bold"
                                                style="color: var(--color-brand-strong);">
                                                {{ $appointment->scheduled_at->format('M d, Y g:i A') }}
                                            </td>
                                            <td>{{ $appointment->reason }}</td>
                                            <td><span class="ht-pill">{{ ucfirst($appointment->status->value) }}</span></td>
                                            @if ($this->canSchedule)
                                                <td>
                                                    <button
                                                        type="button"
                                                        wire:click="deleteAppointment({{ $appointment->id }})"
                                                        wire:confirm="Remove this appointment?"
                                                        class="ht-button ht-button-danger"
                                                    >Remove</button>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            @else
                {{-- Every other tab is a clinical record type. One component
                     handles all of them. --}}
                <livewire:clinical-records
                    :patient="$patient"
                    :type="$section"
                    :key="'records-'.$section.'-'.$patient->id"
                />
            @endif
        </div>
    </div>
</div>
