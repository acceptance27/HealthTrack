<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\Diagnosis;
use App\Models\DoctorNote;
use App\Models\LabValue;
use App\Models\MedicalHistory;
use App\Models\MedicationAllergy;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates a usable demo dataset: three staff-and-patient logins plus 24
 * patients with clinical history.
 *
 * Run with:  php artisan migrate:fresh --seed
 *
 * Every seeded account uses the password "password". That is fine for local
 * development and a demo, and must never be used on a real deployment.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $midwife = User::factory()->midwife()->create([
            'name' => 'Midwife User',
            'email' => 'midwife@healthtrack.test',
            'password' => Hash::make('password'),
        ]);

        User::factory()->healthWorker()->create([
            'name' => 'Health Worker User',
            'email' => 'healthworker@healthtrack.test',
            'password' => Hash::make('password'),
        ]);

        // A patient with a portal login, so the portal can be demonstrated.
        $demoLogin = User::factory()->create([
            'name' => 'Juan Dela Cruz',
            'email' => 'patient@healthtrack.test',
            'password' => Hash::make('password'),
            'role' => UserRole::Patient,
        ]);

        $demoPatient = Patient::factory()->create([
            'user_id' => $demoLogin->id,
            'first_name' => 'Juan',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
            'sex' => 'male',
            'birthdate' => '1991-04-17',
        ]);

        $this->giveClinicalHistory($demoPatient, $midwife);

        // A wider population so the list, search and pagination are worth
        // looking at.
        Patient::factory()
            ->count(21)
            ->create()
            ->each(fn (Patient $patient) => $this->giveClinicalHistory($patient, $midwife));

        // A few more who also have portal logins, as a midwife would have
        // granted. These get clinical history like everyone else -- a patient
        // who can sign in but finds every tab empty makes the portal look
        // broken, which is the opposite of what the demo data is for.
        Patient::factory()
            ->count(3)
            ->withLogin()
            ->create()
            ->each(fn (Patient $patient) => $this->giveClinicalHistory($patient, $midwife));

        $this->command->newLine();
        $this->command->info('Seeded accounts (password: "password"):');
        $this->command->line('  midwife@healthtrack.test       -- Midwife');
        $this->command->line('  healthworker@healthtrack.test  -- Health Worker');
        $this->command->line('  patient@healthtrack.test       -- Patient');
    }

    /** Give one patient a plausible spread of records and appointments. */
    private function giveClinicalHistory(Patient $patient, User $midwife): void
    {
        $for = ['patient_id' => $patient->id, 'created_by' => $midwife->id];

        Diagnosis::factory()->count(rand(1, 4))->create($for);
        LabValue::factory()->count(rand(2, 6))->create($for);
        DoctorNote::factory()->count(rand(1, 3))->create($for);
        MedicalHistory::factory()->count(rand(1, 3))->create($for);
        MedicationAllergy::factory()->count(rand(0, 2))->create($for);

        Appointment::factory()->count(rand(1, 3))->create([
            'patient_id' => $patient->id,
            'midwife_id' => $midwife->id,
        ]);

        Appointment::factory()->upcoming()->create([
            'patient_id' => $patient->id,
            'midwife_id' => $midwife->id,
        ]);
    }
}
