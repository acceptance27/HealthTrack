<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MedicalHistoryController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->patientProfile;
        return view('patient.my-health-information', [
            'patient' => $patient,
            'appointments' => $patient->appointments()->latest('scheduled_at')->get(),
            'allergies' => $patient->allergies()->latest('recorded_at')->get(),
            'diagnoses' => $patient->diagnoses()->latest('diagnosed_at')->get(),
            'doctorNotes' => $patient->doctorNotes()->latest('noted_at')->get(),
            'labValues' => $patient->labValues()->latest('tested_at')->get(),
            'medicalHistories' => $patient->medicalHistories()->latest('recorded_at')->get(),
        ]);
    }
}
