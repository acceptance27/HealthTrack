<?php

namespace App\Livewire\Midwife\Patients;

use App\Models\Diagnosis;
use App\Models\DoctorNote;
use App\Models\LabValue;
use App\Models\MedicalHistory;
use App\Models\MedicationAllergy;
use App\Models\PatientProfile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class PatientRecord extends Component
{
    use AuthorizesRequests;

    public PatientProfile $patient;

    public function mount(PatientProfile $patient): void
    {
        $this->authorize('view', $patient);
        $this->patient = $patient;
    }

    public function render()
    {
        $patientId = $this->patient->user_id;

        return view('livewire.midwife.patients.patient-record', [
            'medicalHistories' => MedicalHistory::where('patient_id', $patientId)->latest('recorded_at')->limit(5)->get(),
            'allergies' => MedicationAllergy::where('patient_id', $patientId)->latest('recorded_at')->limit(5)->get(),
            'diagnoses' => Diagnosis::where('patient_id', $patientId)->latest('diagnosed_at')->limit(5)->get(),
            'labValues' => LabValue::where('patient_id', $patientId)->latest('tested_at')->limit(5)->get(),
            'doctorNotes' => DoctorNote::where('patient_id', $patientId)->latest('noted_at')->limit(5)->get(),
        ]);
    }
}
