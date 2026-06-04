<?php

namespace App\Livewire\Patient;

use App\Models\Appointment;
use App\Models\Diagnosis;
use App\Models\DoctorNote;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        return view('livewire.patient.dashboard', [
            'upcomingAppointments' => Appointment::where('patient_id', $user->id)->where('scheduled_at', '>=', now())->count(),
            'diagnosesCount' => Diagnosis::where('patient_id', $user->id)->count(),
            'notesCount' => DoctorNote::where('patient_id', $user->id)->count(),
        ]);
    }
}
