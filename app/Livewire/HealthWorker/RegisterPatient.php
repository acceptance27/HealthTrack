<?php

namespace App\Livewire\HealthWorker;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Registers a new patient at the health centre.
 *
 * Demographics only. This screen deliberately does not create a portal login:
 * the study's Level 1 DFD gives patient registration to the health worker and
 * account creation to the midwife. A midwife grants portal access afterwards,
 * from the patient's record screen -- see App\Livewire\Patients\Record.
 */
#[Layout('components.layouts.app')]
#[Title('Register Patient')]
class RegisterPatient extends Component
{
    use AuthorizesRequests;

    public string $full_name = '';

    public string $sex = '';

    public string $birthdate = '';

    public string $age = '';

    public string $civil_status = '';

    public string $blood_type = '';

    public string $occupation = '';

    public string $barangay_id_number = '';

    public string $contact_number = '';

    public string $address = '';

    public string $philhealth_number = '';

    public string $emergency_contact_name = '';

    public string $emergency_contact_number = '';

    public function mount(): void
    {
        $this->authorize('register', Patient::class);
    }

    protected function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'sex' => ['required', 'in:female,male'],
            'birthdate' => ['required', 'date', 'before_or_equal:today'],
            'age' => ['required', 'integer', 'min:0', 'max:150'],
            'civil_status' => ['required', 'in:single,married,widowed,separated'],
            'blood_type' => ['required', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'occupation' => ['required', 'string', 'max:255'],
            'barangay_id_number' => ['required', 'string', 'max:50'],
            'contact_number' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:500'],
            'philhealth_number' => ['nullable', 'string', 'max:50'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_number' => ['required', 'string', 'max:50'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'full_name' => 'full name',
            'birthdate' => 'date of birth',
            'age' => 'age',
            'civil_status' => 'civil status',
            'blood_type' => 'blood type',
            'barangay_id_number' => 'Barangay ID number',
            'contact_number' => 'contact number',
            'philhealth_number' => 'PhilHealth number',
            'emergency_contact_name' => 'emergency contact name',
            'emergency_contact_number' => 'emergency contact number',
        ];
    }

    public function save()
    {
        $this->authorize('register', Patient::class);

        $validated = $this->validate();

        $nameParts = preg_split('/\s+/', trim($validated['full_name'])) ?: [];
        $firstName = array_shift($nameParts);
        $lastName = count($nameParts) > 0 ? array_pop($nameParts) : $firstName;
        $middleName = count($nameParts) > 0 ? implode(' ', $nameParts) : null;

        // user_id stays null. A midwife links a portal login later if the
        // patient needs one; most walk-ins never will.
        $patient = Patient::create([
            'user_id' => null,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'sex' => $validated['sex'],
            'birthdate' => $validated['birthdate'],
            'civil_status' => $validated['civil_status'],
            'blood_type' => $validated['blood_type'],
            'occupation' => $validated['occupation'],
            'barangay_id_number' => $validated['barangay_id_number'],
            'contact_number' => $validated['contact_number'] ?: null,
            'address' => $validated['address'],
            'philhealth_number' => $validated['philhealth_number'] ?: null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?: null,
            'emergency_contact_number' => $validated['emergency_contact_number'] ?: null,
        ]);

        session()->flash('status', $patient->fullName().' registered.');

        return $this->redirectRoute('patients.show', ['patient' => $patient], navigate: true);
    }

    public function render()
    {
        return view('livewire.health-worker.register-patient');
    }
}
