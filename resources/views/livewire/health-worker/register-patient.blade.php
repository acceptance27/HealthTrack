<div class="grid gap-4">
    <x-page-header
        title="Register Patient"
        subtitle="Add a new patient to {{ config('healthtrack.centre.name') }}."
    >
        <x-slot:aside>
            <a href="{{ route('patients.index') }}" class="ht-button ht-button-muted">
                <svg class="ht-button-icon" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                <span>Back to patients</span>
            </a>
        </x-slot:aside>
    </x-page-header>

    <form wire:submit="save" class="grid gap-4">

        <div class="ht-panel">
            <h2>
                <span class="ht-section-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="3" />
                        <path d="M5 20a7 7 0 0 1 14 0" />
                    </svg>
                </span>
                Personal Information
            </h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <x-field name="full_name" label="Full Name" placeholder="Enter full name" :required="true" />

                <x-field name="sex" label="Sex" placeholder="Enter sex" :required="true" />
                <x-field name="birthdate" label="Date of Birth" type="date" :required="true" />

                <x-field name="age" label="Age" type="number" placeholder="Enter age" :required="true" />
                <x-field name="civil_status" label="Civil Status" placeholder="Enter civil status" :required="true" />
                <x-field name="blood_type" label="Blood Type" type="select" :options="['A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'AB+' => 'AB+', 'AB-' => 'AB-', 'O+' => 'O+', 'O-' => 'O-']" :required="true" />

                <x-field name="occupation" label="Occupation" placeholder="Enter occupation" :required="true" />
                <x-field name="contact_number" label="Contact Number" placeholder="Enter contact number" :required="true" />
                <x-field name="barangay_id_number" label="Barangay ID Number" placeholder="Enter barangay ID number" :required="true" />
            </div>
        </div>

        <div class="ht-panel">
            <h2>
                <span class="ht-section-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />
                        <circle cx="12" cy="10" r="2.5" />
                    </svg>
                </span>
                Address Information
            </h2>
            <x-field name="address" label="Complete Address" type="textarea" placeholder="House/Unit No., Street, Barangay, City/Municipality, Province" :required="true" />
        </div>

        <div class="ht-panel">
            <h2>
                <span class="ht-section-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.8 2.1Z" />
                    </svg>
                </span>
                Emergency Contact
            </h2>
            <div class="grid gap-3 sm:grid-cols-2">
                <x-field name="emergency_contact_name" label="Emergency Contact Name" placeholder="Enter emergency contact name" :required="true" />
                <x-field name="emergency_contact_number" label="Emergency Contact Number" placeholder="Enter emergency contact number" :required="true" />
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="ht-button" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Register patient</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
            <a href="{{ route('patients.index') }}" class="ht-button ht-button-muted">Cancel</a>
        </div>
    </form>
</div>