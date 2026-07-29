<?php

/*
|--------------------------------------------------------------------------
| Role access
|--------------------------------------------------------------------------
|
| Confirms each role can reach its own pages and is refused everyone else's.
| These guard the route middleware in routes/web.php.
|
*/

use App\Models\Patient;
use App\Models\User;

it('sends each role to its own dashboard after login', function (string $state, string $path) {
    $user = User::factory()->{$state}()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect($path);
})->with([
    ['midwife', '/midwife/dashboard'],
    ['healthWorker', '/health-worker/dashboard'],
    ['patient', '/patient/dashboard'],
]);

it('refuses the midwife dashboard to everyone else', function (string $state) {
    $this->actingAs(User::factory()->{$state}()->create())
        ->get('/midwife/dashboard')
        ->assertForbidden();
})->with(['healthWorker', 'patient']);

it('refuses patient registration to anyone but a health worker', function (string $state) {
    $this->actingAs(User::factory()->{$state}()->create())
        ->get('/health-worker/register-patient')
        ->assertForbidden();
})->with(['midwife', 'patient']);

it('refuses the patient list to patients', function () {
    $this->actingAs(User::factory()->patient()->create())
        ->get('/patients')
        ->assertForbidden();
});

it('lets both staff roles open the patient list', function (string $state) {
    $this->actingAs(User::factory()->{$state}()->create())
        ->get('/patients')
        ->assertOk();
})->with(['midwife', 'healthWorker']);

it('refuses a patient record to another patient', function () {
    $patient = Patient::factory()->withLogin()->create();
    $someoneElse = User::factory()->patient()->create();

    // Patients have no route into the staff record screen at all -- the
    // role middleware stops them before any policy is consulted.
    $this->actingAs($someoneElse)
        ->get("/patients/{$patient->id}")
        ->assertForbidden();
});

it('sends guests to the login page', function () {
    $this->get('/patients')->assertRedirect('/login');
});

it('does not expose a registration page', function () {
    // Public registration is switched off in config/fortify.php. If this test
    // fails, someone has re-enabled Features::registration() -- which would
    // let anyone create their own account on a clinical system.
    $this->get('/register')->assertNotFound();
});
