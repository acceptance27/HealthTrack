<?php

namespace App\Livewire\PatientRegistry;

use App\Enums\AppointmentStatus;
use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * One patient's full record, as seen by staff.
 *
 * The clinical tabs are rendered by the shared ClinicalRecords component, so
 * this class only handles the patient's own details and their appointments.
 */
#[Layout('components.layouts.app')]
class Record extends Component
{
    use AuthorizesRequests;

    public Patient $patient;

    /** Which tab is open: "general" or a key from config('healthtrack.records'). */
    #[Url(except: 'general')]
    public string $section = 'general';

    // --- Appointment form -------------------------------------------------

    public string $scheduledAt = '';

    public string $reason = '';

    public string $notes = '';

    public string $status = 'pending';

    public bool $showAppointmentForm = false;

    // --- Portal account ---------------------------------------------------

    public string $portalEmail = '';

    public bool $showAccountForm = false;

    public function mount(Patient $patient): void
    {
        $this->authorize('view', $patient);

        $this->patient = $patient;

        if (! array_key_exists($this->section, $this->sections())) {
            $this->section = 'general';
        }
    }

    /** Tab label for each section, built from the record config. */
    public function sections(): array
    {
        $sections = ['general' => 'General'];

        foreach (config('healthtrack.records') as $key => $definition) {
            $sections[$key] = $definition['label'];
        }

        return $sections;
    }

    #[Computed]
    public function canSchedule(): bool
    {
        return auth()->user()->can('create', Appointment::class);
    }

    public function toggleAppointmentForm(): void
    {
        $this->authorize('create', Appointment::class);

        $this->showAppointmentForm = ! $this->showAppointmentForm;

        if (! $this->showAppointmentForm) {
            $this->resetAppointmentForm();
        }
    }

    public function scheduleAppointment(): void
    {
        $this->authorize('create', Appointment::class);

        $validated = $this->validate([
            'scheduledAt' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:pending,confirmed,completed,cancelled'],
        ], attributes: [
            'scheduledAt' => 'date and time',
        ]);

        Appointment::create([
            'patient_id' => $this->patient->id,
            'midwife_id' => auth()->id(),
            'scheduled_at' => $validated['scheduledAt'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?: null,
            'status' => $validated['status'],
        ]);

        $this->resetAppointmentForm();
        $this->showAppointmentForm = false;

        session()->flash('status', 'Appointment scheduled.');
    }

    public function deleteAppointment(int $id): void
    {
        $appointment = Appointment::where('patient_id', $this->patient->id)->findOrFail($id);

        $this->authorize('delete', $appointment);

        $appointment->delete();

        session()->flash('status', 'Appointment removed.');
    }

    #[Computed]
    public function canCreateAccount(): bool
    {
        return auth()->user()->can('createAccount', $this->patient);
    }

    public function toggleAccountForm(): void
    {
        $this->authorize('createAccount', $this->patient);

        $this->showAccountForm = ! $this->showAccountForm;

        if (! $this->showAccountForm) {
            $this->reset('portalEmail');
            $this->resetValidation();
        }
    }

    /**
     * Give this patient a login for the portal.
     *
     * No password is chosen here. The account is created with an unusable
     * random string, and the patient sets a real password through the
     * "forgot password" flow -- so no member of staff ever types, sees or
     * knows a patient's password.
     */
    public function createPortalAccount(): void
    {
        $this->authorize('createAccount', $this->patient);

        // Nothing to do if a login already exists; without this a second
        // submission would orphan the first user record.
        if ($this->patient->user_id) {
            return;
        }

        $validated = $this->validate([
            'portalEmail' => ['required', 'email', 'max:255', 'unique:users,email'],
        ], attributes: [
            'portalEmail' => 'email address',
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'name' => $this->patient->fullName(),
                'email' => $validated['portalEmail'],
                'role' => UserRole::Patient,
                'password' => Str::random(40),
            ]);

            $this->patient->update(['user_id' => $user->id]);
        });

        $this->patient->refresh();
        $this->reset('portalEmail');
        $this->showAccountForm = false;

        session()->flash('status', 'Portal account created. Ask the patient to use "Forgot password" to set their own password.');
    }

    private function resetAppointmentForm(): void
    {
        $this->reset(['scheduledAt', 'reason', 'notes']);
        $this->status = 'pending';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.patient-registry.record', [
            'sections' => $this->sections(),
            'appointments' => $this->patient
                ->appointments()
                ->orderByDesc('scheduled_at')
                ->get(),
            'statuses' => AppointmentStatus::cases(),
        ]);
    }
}
