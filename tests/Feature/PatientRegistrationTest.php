<?php

/*
|--------------------------------------------------------------------------
| Patient registration
|--------------------------------------------------------------------------
|
| Registering patients is the health worker's module in the study's hierarchy.
|
| Demographics only -- this screen never creates a portal login. That is the
| midwife's job, covered by PortalAccountTest.
|
*/

use App\Enums\UserRole;
use App\Livewire\HealthWorker\RegisterPatient;
use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;

function validPatientDetails(): array
{
    return [
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'sex' => 'female',
        'birthdate' => '1995-03-12',
        'address' => '12 Mabini Street, Mambog I',
        'contact_number' => '0917 555 1234',
    ];
}

it('registers a patient and never creates a login', function () {
    $healthWorker = User::factory()->healthWorker()->create();

    $component = Livewire::actingAs($healthWorker)->test(RegisterPatient::class);

    foreach (validPatientDetails() as $field => $value) {
        $component->set($field, $value);
    }

    $component->call('save')->assertHasNoErrors();

    $patient = Patient::sole();

    expect($patient->fullName())->toBe('Santos, Maria')
        ->and($patient->user_id)->toBeNull()
        ->and(User::where('role', UserRole::Patient)->count())->toBe(0);
});

it('offers no way to create a login from this screen', function () {
    // Guards the split: if someone re-adds an email or toggle here, the
    // midwife-only rule has been quietly undone.
    Livewire::actingAs(User::factory()->healthWorker()->create())
        ->test(RegisterPatient::class)
        ->assertOk()
        ->assertDontSee('Portal access')
        ->assertDontSee('Email address');
});

it('validates the required demographic fields', function () {
    $healthWorker = User::factory()->healthWorker()->create();

    Livewire::actingAs($healthWorker)
        ->test(RegisterPatient::class)
        ->call('save')
        ->assertHasErrors(['first_name', 'last_name', 'sex', 'birthdate', 'address']);
});

it('rejects a birthdate in the future', function () {
    $healthWorker = User::factory()->healthWorker()->create();

    $component = Livewire::actingAs($healthWorker)->test(RegisterPatient::class);

    foreach (validPatientDetails() as $field => $value) {
        $component->set($field, $value);
    }

    $component->set('birthdate', now()->addYear()->format('Y-m-d'))
        ->call('save')
        ->assertHasErrors(['birthdate']);
});

it('stops a midwife from using the health worker registration screen', function () {
    Livewire::actingAs(User::factory()->midwife()->create())
        ->test(RegisterPatient::class)
        ->assertForbidden();
});
