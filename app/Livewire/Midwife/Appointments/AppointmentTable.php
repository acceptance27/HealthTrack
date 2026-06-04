<?php

namespace App\Livewire\Midwife\Appointments;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;

class AppointmentTable extends Component
{
    use WithPagination;

    public string $status = '';

    public function render()
    {
        return view('livewire.midwife.appointments.appointment-table', [
            'appointments' => Appointment::forBarangay(auth()->user()->barangay_id)
                ->when($this->status, fn ($query) => $query->where('status', $this->status))
                ->latest('scheduled_at')
                ->paginate(10),
        ]);
    }
}
