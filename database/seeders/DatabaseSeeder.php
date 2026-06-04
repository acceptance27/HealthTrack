<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Barangay;
use App\Models\InventoryItem;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $barangay = Barangay::create([
            'name' => 'San Isidro',
            'municipality' => 'Sample Municipality',
            'province' => 'Sample Province',
            'region' => 'Region IV-A',
        ]);

        User::factory()->admin()->create([
            'barangay_id' => $barangay->id,
            'name' => 'Admin User',
            'email' => 'admin@healthtrack.test',
        ]);

        User::factory()->midwife()->create([
            'barangay_id' => $barangay->id,
            'name' => 'Midwife User',
            'email' => 'midwife@healthtrack.test',
        ]);

        $patientUser = User::factory()->create([
            'barangay_id' => $barangay->id,
            'name' => 'Juan Dela Cruz',
            'email' => 'delacruz.juan@healthtrack.test',
        ]);

        PatientProfile::factory()->create([
            'user_id' => $patientUser->id,
            'barangay_id' => $barangay->id,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
        ]);

        PatientProfile::factory()->count(9)->create(['barangay_id' => $barangay->id]);

        $midwife = User::where('role', 'midwife')->first();

        // Add sample data for patients
        $patients = PatientProfile::where('barangay_id', $barangay->id)->get();
        foreach ($patients as $patient) {
            // Create some appointments
            \App\Models\Appointment::factory()->count(rand(1, 3))->create([
                'patient_id' => $patient->id,
                'barangay_id' => $barangay->id,
                'midwife_id' => $midwife->id,
            ]);

            // Create some diagnoses
            \App\Models\Diagnosis::factory()->count(rand(0, 2))->create([
                'patient_id' => $patient->id,
                'barangay_id' => $barangay->id,
                'created_by' => $midwife->id,
            ]);

            // Create some doctor notes
            \App\Models\DoctorNote::factory()->count(rand(0, 2))->create([
                'patient_id' => $patient->id,
                'barangay_id' => $barangay->id,
                'created_by' => $midwife->id,
            ]);

            // Create some lab values
            \App\Models\LabValue::factory()->count(rand(0, 3))->create([
                'patient_id' => $patient->id,
                'barangay_id' => $barangay->id,
                'created_by' => $midwife->id,
            ]);

            // Create some medical histories
            \App\Models\MedicalHistory::factory()->count(rand(0, 2))->create([
                'patient_id' => $patient->id,
                'barangay_id' => $barangay->id,
                'created_by' => $midwife->id,
            ]);

            // Create some allergies
            \App\Models\MedicationAllergy::factory()->count(rand(0, 1))->create([
                'patient_id' => $patient->id,
                'barangay_id' => $barangay->id,
                'created_by' => $midwife->id,
            ]);
        }

        InventoryItem::create([
            'barangay_id' => $barangay->id,
            'name' => 'Paracetamol',
            'type' => 'medicine',
            'unit' => 'tablet',
            'quantity_on_hand' => 100,
            'reorder_level' => 20,
        ]);

        InventoryItem::create([
            'barangay_id' => $barangay->id,
            'name' => 'BCG Vaccine',
            'type' => 'vaccine',
            'unit' => 'dose',
            'quantity_on_hand' => 30,
            'reorder_level' => 10,
        ]);
    }
}
