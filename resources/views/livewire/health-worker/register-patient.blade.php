<div class="grid gap-4">
    <x-page-header
        title="Register Patient"
        subtitle="Add a new patient to {{ config('healthtrack.centre.name') }}."
    >
        <x-slot:aside>
            <a href="{{ route('patients.index') }}" class="ht-button ht-button-muted">Back to patients</a>
        </x-slot:aside>
    </x-page-header>

    <form wire:submit="save" class="grid gap-4">

        <div class="ht-panel">
            <h2>Personal details</h2>
            <div class="grid gap-3 sm:grid-cols-2">
                <x-field name="first_name" label="First name" :required="true" />
                <x-field name="middle_name" label="Middle name" />
                <x-field name="last_name" label="Last name" :required="true" />

                <label class="ht-field">
                    <span>Sex <span style="color: var(--color-danger);">*</span></span>
                    <select wire:model="sex" class="ht-input">
                        <option value="">-- Select --</option>
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                    </select>
                    @error('sex') <span class="ht-error">{{ $message }}</span> @enderror
                </label>

                <x-field name="birthdate" label="Date of birth" type="date" :required="true" />
                <x-field name="contact_number" label="Contact number" placeholder="09XX XXX XXXX" />
            </div>
        </div>

        <div class="ht-panel">
            <h2>Address and identification</h2>
            <div class="grid gap-3">
                <x-field name="address" label="Address" type="textarea" :required="true" />
                <div class="grid gap-3 sm:grid-cols-2">
                    <x-field name="philhealth_number" label="PhilHealth number" />
                </div>
            </div>
        </div>

        <div class="ht-panel">
            <h2>Emergency contact</h2>
            <div class="grid gap-3 sm:grid-cols-2">
                <x-field name="emergency_contact_name" label="Contact name" />
                <x-field name="emergency_contact_number" label="Contact number" />
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
