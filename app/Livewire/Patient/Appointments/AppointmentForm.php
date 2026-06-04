<?php

namespace App\Livewire\Patient\Appointments;

use App\Models\Appointment;
use Livewire\Component;

class AppointmentForm extends Component
{
    public string $scheduled_at = '';
    public string $reason = '';

    public function save(): void
    {
        $data = $this->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        Appointment::create([
            'barangay_id' => auth()->user()->barangay_id,
            'patient_id' => auth()->id(),
            'scheduled_at' => $data['scheduled_at'],
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        $this->reset(['scheduled_at', 'reason']);
        $this->dispatch('appointment-created');
    }

    public function render()
    {
        return view('livewire.patient.appointments.appointment-form');
    }
}
