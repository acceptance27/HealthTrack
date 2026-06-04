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

class AllPatientsSeeder extends Seeder
{
    public function run()
    {
        $patients = PatientProfile::all();
        $midwife = User::where('role', 'midwife')->first();
        $midwifeId = $midwife ? $midwife->id : null;

        if ($patients->isEmpty()) {
            $this->command->error('No patients found.');
            return;
        }

        foreach ($patients as $patient) {
            $userId = $patient->user_id;
            $barangayId = $patient->barangay_id;

            // Populate Allergies
            if ($patient->allergies()->count() === 0) {
                MedicationAllergy::create([
                    'barangay_id' => $barangayId,
                    'patient_id' => $userId,
                    'allergen' => 'Penicillin',
                    'reaction' => 'Skin rash and itching',
                    'severity' => 'Moderate',
                    'recorded_at' => Carbon::now()->subMonths(rand(1, 12)),
                ]);
                if (rand(0, 1)) {
                    MedicationAllergy::create([
                        'barangay_id' => $barangayId,
                        'patient_id' => $userId,
                        'allergen' => 'Aspirin',
                        'reaction' => 'Shortness of breath',
                        'severity' => 'Severe',
                        'recorded_at' => Carbon::now()->subMonths(rand(13, 24)),
                    ]);
                }
            }

            // Populate Diagnoses
            if ($patient->diagnoses()->count() === 0) {
                Diagnosis::create([
                    'barangay_id' => $barangayId,
                    'patient_id' => $userId,
                    'diagnosis' => 'Essential Hypertension',
                    'description' => 'Blood pressure consistently above 140/90 mmHg.',
                    'diagnosed_at' => Carbon::now()->subMonths(rand(1, 6)),
                ]);
                if (rand(0, 1)) {
                    Diagnosis::create([
                        'barangay_id' => $barangayId,
                        'patient_id' => $userId,
                        'diagnosis' => 'Type 2 Diabetes Mellitus',
                        'description' => 'Controlled with diet and metformin.',
                        'diagnosed_at' => Carbon::now()->subMonths(rand(7, 12)),
                    ]);
                }
            }

            // Populate Doctor Notes
            if ($patient->doctorNotes()->count() === 0) {
                DoctorNote::create([
                    'barangay_id' => $barangayId,
                    'patient_id' => $userId,
                    'title' => 'Routine Consultation',
                    'note' => 'Patient is doing well. Vitals are within normal range.',
                    'noted_at' => Carbon::now()->subWeeks(rand(1, 4)),
                ]);
            }

            // Populate Lab Values
            if ($patient->labValues()->count() === 0) {
                LabValue::create([
                    'barangay_id' => $barangayId,
                    'patient_id' => $userId,
                    'test_name' => 'Fastest Blood Sugar (FBS)',
                    'value' => (string)rand(90, 110),
                    'unit' => 'mg/dL',
                    'reference_range' => '70-99',
                    'tested_at' => Carbon::now()->subWeeks(rand(1, 2)),
                ]);
            }

            // Populate Medical History
            if ($patient->medicalHistories()->count() === 0) {
                MedicalHistory::create([
                    'barangay_id' => $barangayId,
                    'patient_id' => $userId,
                    'condition' => 'Previous Hospitalization',
                    'details' => 'Patient was hospitalized for mild fever 2 years ago.',
                    'recorded_at' => Carbon::now()->subYears(2),
                ]);
            }

            // Populate Appointments
            if ($patient->appointments()->count() === 0) {
                Appointment::create([
                    'barangay_id' => $barangayId,
                    'patient_id' => $userId,
                    'midwife_id' => $midwifeId,
                    'scheduled_at' => Carbon::now()->addDays(rand(1, 10))->setHour(rand(8, 16))->setMinute(0),
                    'status' => 'pending',
                    'reason' => 'Follow-up checkup',
                ]);
                Appointment::create([
                    'barangay_id' => $barangayId,
                    'patient_id' => $userId,
                    'midwife_id' => $midwifeId,
                    'scheduled_at' => Carbon::now()->subMonths(rand(1, 2))->setHour(rand(8, 16))->setMinute(0),
                    'status' => 'completed',
                    'reason' => 'Monthly Monitoring',
                ]);
            }
        }

        $this->command->info('Records for all patients filled out successfully.');
    }
}
