<?php

namespace App\Livewire\Midwife\Patients;

use App\Models\PatientProfile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class PatientEdit extends Component
{
    use AuthorizesRequests;

    public PatientProfile $patient;

    public function mount(PatientProfile $patient): void
    {
        $this->authorize('update', $patient);
        $this->patient = $patient;
    }

    public function render()
    {
        return view('livewire.midwife.patients.patient-edit');
    }
}
