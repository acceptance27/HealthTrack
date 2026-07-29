<?php

/*
|--------------------------------------------------------------------------
| Demo data
|--------------------------------------------------------------------------
|
| The seeder exists to make the system demonstrable, so the shape of what it
| produces is worth asserting. In particular: every patient who can log in
| must have something to look at. An earlier version gave portal logins to
| three patients and clinical history to a different twenty-one, so signing in
| as a patient showed five empty tabs.
|
*/

use App\Enums\UserRole;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

it('gives every patient with a login something to look at', function () {
    $this->seed(DatabaseSeeder::class);

    $withLogin = Patient::whereNotNull('user_id')->get();

    expect($withLogin)->not->toBeEmpty();

    foreach ($withLogin as $patient) {
        $records = $patient->diagnoses()->count()
            + $patient->labValues()->count()
            + $patient->doctorNotes()->count()
            + $patient->medicalHistories()->count()
            + $patient->allergies()->count();

        expect($records)->toBeGreaterThan(
            0,
            "Patient {$patient->id} ({$patient->user->email}) can sign in but has no clinical records."
        );
    }
});

it('gives every seeded patient a clinical history', function () {
    $this->seed(DatabaseSeeder::class);

    foreach (Patient::all() as $patient) {
        expect($patient->diagnoses()->count())->toBeGreaterThan(0)
            ->and($patient->appointments()->count())->toBeGreaterThan(0);
    }
});

it('seeds the three documented staff and patient logins', function () {
    $this->seed(DatabaseSeeder::class);

    // These addresses are printed in the README; if they change, that changes.
    expect(User::where('email', 'midwife@healthtrack.test')->value('role'))->toBe(UserRole::Midwife)
        ->and(User::where('email', 'healthworker@healthtrack.test')->value('role'))->toBe(UserRole::HealthWorker)
        ->and(User::where('email', 'patient@healthtrack.test')->value('role'))->toBe(UserRole::Patient);
});
