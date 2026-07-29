<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('My Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $patient = auth()->user()->patient;

        // A patient account should always have a matching patient record, but
        // if the link is missing show an empty state rather than a 500.
        if (! $patient instanceof Patient) {
            return view('livewire.patient.no-record');
        }

        return view('livewire.patient.dashboard', [
            'patient' => $patient,
            'upcomingAppointments' => $patient->appointments()
                ->upcoming()
                ->orderBy('scheduled_at')
                ->limit(5)
                ->get(),
            'upcomingCount' => $patient->appointments()->upcoming()->count(),
            'diagnosisCount' => $patient->diagnoses()->count(),
            'allergyCount' => $patient->allergies()->count(),
        ]);
    }
}
