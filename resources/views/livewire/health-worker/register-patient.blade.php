<div class="grid gap-4">

    <x-page-header
        title="Register Patient"
        subtitle="Add a new patient to {{ config('healthtrack.centre.name') }}."
    >
        <x-slot:aside>
            <a href="{{ route('patients.index') }}" class="ht-button ht-button-muted">
                Back to patients
            </a>
        </x-slot:aside>
    </x-page-header>

    <form wire:submit="save" class="grid gap-4">

        {{-- PERSONAL INFORMATION --}}
        <div class="ht-panel">

            <div class="flex items-center gap-3 mb-4">
                <div
                    style="
                        width: 40px;
                        height: 40px;
                        border-radius: 10px;
                        background: #eaf7f2;
                        color: var(--color-brand);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 20px;
                        flex-shrink: 0;
                    "
                >
                    &#128100;
                </div>

                <div>
                    <h2 style="margin: 0;">Personal Information</h2>
                    <p style="margin: 2px 0 0; color: #6b7280; font-size: 14px;">
                        Enter the patient's basic information.
                    </p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

                <x-field
                    name="first_name"
                    label="First Name"
                    placeholder="Enter first name"
                    :required="true"
                />

                <x-field
                    name="middle_name"
                    label="Middle Name"
                    placeholder="Enter middle name"
                />

                <x-field
                    name="last_name"
                    label="Last Name"
                    placeholder="Enter last name"
                    :required="true"
                />

                <label class="ht-field">
                    <span>
                        Sex
                        <span style="color: var(--color-danger);">*</span>
                    </span>

                    <select wire:model="sex" class="ht-input">
                        <option value="">Select sex</option>
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                    </select>

                    @error('sex')
                        <span class="ht-error">{{ $message }}</span>
                    @enderror
                </label>

                <x-field
                    name="birthdate"
                    label="Date of Birth"
                    type="date"
                    :required="true"
                />

                <label class="ht-field">
                    <span>Age</span>
                    <input
                        type="text"
                        value="{{ $birthdate ? \Carbon\Carbon::parse($birthdate)->age : '' }}"
                        placeholder="Calculated from date of birth"
                        class="ht-input"
                        readonly
                    >
                </label>

                <x-field
                    name="civil_status"
                    label="Civil Status"
                    type="select"
                    :options="[
                        'single' => 'Single',
                        'married' => 'Married',
                        'widowed' => 'Widowed',
                        'separated' => 'Separated',
                    ]"
                    :required="true"
                />

                <x-field
                    name="blood_type"
                    label="Blood Type"
                    type="select"
                    :options="[
                        'A+' => 'A+', 'A-' => 'A-',
                        'B+' => 'B+', 'B-' => 'B-',
                        'AB+' => 'AB+', 'AB-' => 'AB-',
                        'O+' => 'O+', 'O-' => 'O-',
                    ]"
                    :required="true"
                />

                <x-field
                    name="occupation"
                    label="Occupation"
                    placeholder="Enter occupation"
                    :required="true"
                />

                <x-field
                    name="contact_number"
                    label="Contact Number"
                    placeholder="09XX XXX XXXX"
                    :required="true"
                />

                <x-field
                    name="barangay_id_number"
                    label="Barangay ID Number"
                    placeholder="Enter barangay ID number"
                    :required="true"
                />

            </div>
        </div>


        {{-- ADDRESS & IDENTIFICATION --}}
        <div class="ht-panel">

            <div class="flex items-center gap-3 mb-4">
                <div
                    style="
                        width: 40px;
                        height: 40px;
                        border-radius: 10px;
                        background: #eaf7f2;
                        color: var(--color-brand);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 20px;
                        flex-shrink: 0;
                    "
                >
                    &#128205;
                </div>

                <div>
                    <h2 style="margin: 0;">Address & Identification</h2>
                    <p style="margin: 2px 0 0; color: #6b7280; font-size: 14px;">
                        Provide the patient's address and identification details.
                    </p>
                </div>
            </div>

            <div class="grid gap-3">

                <x-field
                    name="address"
                    label="Complete Address"
                    type="textarea"
                    placeholder="Enter complete address"
                    :required="true"
                />

                <div class="grid gap-3 sm:grid-cols-2">

                    <x-field
                        name="philhealth_number"
                        label="PhilHealth Number"
                        placeholder="Enter PhilHealth number"
                    />

                </div>

            </div>
        </div>


        {{-- EMERGENCY CONTACT --}}
        <div class="ht-panel">

            <div class="flex items-center gap-3 mb-4">
                <div
                    style="
                        width: 40px;
                        height: 40px;
                        border-radius: 10px;
                        background: #fff4e8;
                        color: var(--color-brand-warm);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 20px;
                        flex-shrink: 0;
                    "
                >
                    &#128222;
                </div>

                <div>
                    <h2 style="margin: 0;">Emergency Contact</h2>
                    <p style="margin: 2px 0 0; color: #6b7280; font-size: 14px;">
                        Add someone we can contact in case of emergency.
                    </p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">

                <x-field
                    name="emergency_contact_name"
                    label="Contact Name"
                    placeholder="Enter contact name"
                    :required="true"
                />

                <x-field
                    name="emergency_contact_number"
                    label="Contact Number"
                    placeholder="09XX XXX XXXX"
                    :required="true"
                />

            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex gap-2 justify-end">

            <a
                href="{{ route('patients.index') }}"
                class="ht-button ht-button-muted"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="ht-button"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="save">
                    Register Patient
                </span>

                <span wire:loading wire:target="save">
                    Saving...
                </span>
            </button>

        </div>

    </form>
</div>