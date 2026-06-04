<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PatientProfile;
use App\Models\MedicationAllergy;
use App\Models\Diagnosis;
use App\Models\DoctorNote;
use App\Models\LabValue;
use App\Models\MedicalHistory;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class PatientNineSeeder extends Seeder
{
    public function run()
    {
        $patient = PatientProfile::find(9);

        if (!$patient) {
            $this->command->error('Patient with ID 9 not found.');
            return;
        }

        $userId = $patient->user_id;
        $barangayId = $patient->barangay_id;
        $midwife = User::where('role', 'midwife')->first();
        $midwifeId = $midwife ? $midwife->id : null;

        // Populate Allergies
        if ($patient->allergies()->count() === 0) {
            MedicationAllergy::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'allergen' => 'Penicillin',
                'reaction' => 'Skin rash and itching',
                'severity' => 'Moderate',
                'recorded_at' => Carbon::now()->subMonths(6),
            ]);
            MedicationAllergy::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'allergen' => 'Aspirin',
                'reaction' => 'Shortness of breath',
                'severity' => 'Severe',
                'recorded_at' => Carbon::now()->subYear(),
            ]);
        }

        // Populate Diagnoses
        if ($patient->diagnoses()->count() === 0) {
            Diagnosis::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'diagnosis' => 'Essential Hypertension',
                'description' => 'Blood pressure consistently above 140/90 mmHg.',
                'diagnosed_at' => Carbon::now()->subMonths(3),
            ]);
            Diagnosis::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'diagnosis' => 'Type 2 Diabetes Mellitus',
                'description' => 'Controlled with diet and metformin.',
                'diagnosed_at' => Carbon::now()->subMonths(8),
            ]);
        }

        // Populate Doctor Notes
        if ($patient->doctorNotes()->count() === 0) {
            DoctorNote::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'title' => 'Follow-up Consultation',
                'note' => 'Patient reports feeling better. BP is stable at 130/85. Continue current medications.',
                'noted_at' => Carbon::now()->subWeeks(2),
            ]);
            DoctorNote::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'title' => 'Initial Assessment',
                'note' => 'Patient presented with mild headaches. Recommended lifestyle changes.',
                'noted_at' => Carbon::now()->subMonths(4),
            ]);
        }

        // Populate Lab Values
        if ($patient->labValues()->count() === 0) {
            LabValue::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'test_name' => 'Fastest Blood Sugar (FBS)',
                'value' => '105',
                'unit' => 'mg/dL',
                'reference_range' => '70-99',
                'tested_at' => Carbon::now()->subWeeks(1),
            ]);
            LabValue::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'test_name' => 'Hemoglobin A1c',
                'value' => '6.4',
                'unit' => '%',
                'reference_range' => '< 5.7',
                'tested_at' => Carbon::now()->subMonths(2),
            ]);
        }

        // Populate Medical History
        if ($patient->medicalHistories()->count() === 0) {
            MedicalHistory::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'condition' => 'Appendectomy',
                'details' => 'Surgical removal of the appendix at age 15.',
                'recorded_at' => Carbon::create(2015, 5, 20),
            ]);
            MedicalHistory::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'condition' => 'Family History of Heart Disease',
                'details' => 'Father had a myocardial infarction at age 55.',
                'recorded_at' => Carbon::now()->subYears(2),
            ]);
        }

        // Populate Appointments
        if ($patient->appointments()->count() === 0) {
            Appointment::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'midwife_id' => $midwifeId,
                'scheduled_at' => Carbon::now()->addDays(3)->setHour(10)->setMinute(0),
                'status' => 'pending',
                'reason' => 'Routine prenatal checkup',
            ]);
            Appointment::create([
                'barangay_id' => $barangayId,
                'patient_id' => $userId,
                'midwife_id' => $midwifeId,
                'scheduled_at' => Carbon::now()->subMonths(1)->setHour(14)->setMinute(30),
                'status' => 'completed',
                'reason' => 'Blood pressure monitoring',
            ]);
        }

        $this->command->info('Records for patient ID 9 filled out successfully.');
    }
}
