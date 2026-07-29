<?php

namespace App\Livewire\Midwife;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Centre-wide appointment list.
 *
 * Appointments are created from a patient's record screen (Patients\Record),
 * because scheduling one always means picking a patient first. This page is
 * for reviewing and updating what is already booked.
 */
#[Layout('components.layouts.app')]
#[Title('Appointments')]
class Appointments extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(except: 'upcoming')]
    public string $filter = 'upcoming';

    public function mount(): void
    {
        $this->authorize('viewAny', Appointment::class);
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    /** Move an appointment to a new status from the list. */
    public function setStatus(int $id, string $status): void
    {
        $appointment = Appointment::findOrFail($id);

        $this->authorize('update', $appointment);

        // The value comes from the browser, so confirm it is a real case
        // rather than trusting it into the enum cast.
        $newStatus = AppointmentStatus::tryFrom($status);

        abort_if($newStatus === null, 422, 'Unknown appointment status.');

        $appointment->update(['status' => $newStatus]);

        session()->flash('status', 'Appointment updated.');
    }

    public function render()
    {
        $query = Appointment::with('patient');

        $appointments = match ($this->filter) {
            'today' => $query->today()->orderBy('scheduled_at'),
            'past' => $query->where('scheduled_at', '<', now())->orderByDesc('scheduled_at'),
            'all' => $query->orderByDesc('scheduled_at'),
            default => $query->upcoming()->orderBy('scheduled_at'),
        };

        return view('livewire.midwife.appointments', [
            'appointments' => $appointments->paginate(20),
            'statuses' => AppointmentStatus::cases(),
        ]);
    }
}
