<?php

namespace App\Livewire\Patient\Appointments;

use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;

class AppointmentList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.patient.appointments.appointment-list', [
            'appointments' => Appointment::where('patient_id', auth()->id())->latest('scheduled_at')->paginate(10),
        ]);
    }
}
