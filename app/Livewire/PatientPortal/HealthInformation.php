<?php

namespace App\Livewire\PatientPortal;

use App\Models\Patient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * The patient's own records, read-only.
 *
 * Reuses the same ClinicalRecords component the midwife uses, passed
 * read-only, so the two views can never drift apart.
 */
#[Layout('components.layouts.app')]
#[Title('My Health Information')]
class HealthInformation extends Component
{
    public function render()
    {
        $patient = auth()->user()->patient;

        if (! $patient instanceof Patient) {
            return view('livewire.patient-portal.no-record');
        }

        return view('livewire.patient-portal.health-information', [
            'patient' => $patient,
            'recordTypes' => config('healthtrack.records'),
            'appointments' => $patient->appointments()
                ->orderByDesc('scheduled_at')
                ->get(),
        ]);
    }
}
