<?php

namespace App\Http\Controllers\Midwife;

use App\Http\Controllers\Controller;
use App\Models\MedicalHistory;
use App\Models\Diagnosis;
use App\Models\LabValue;
use App\Models\DoctorNote;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $barangayId = Auth::user()->barangay_id;

        return view('midwife.medical-records.index', [
            'medicalHistories' => MedicalHistory::where('barangay_id', $barangayId)->paginate(20),
            'diagnoses' => Diagnosis::where('barangay_id', $barangayId)->paginate(20),
            'labValues' => LabValue::where('barangay_id', $barangayId)->paginate(20),
            'doctorNotes' => DoctorNote::where('barangay_id', $barangayId)->paginate(20),
        ]);
    }
}
