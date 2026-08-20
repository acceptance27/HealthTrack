<?php

/*
|--------------------------------------------------------------------------
| Patient portal accounts
|--------------------------------------------------------------------------
|
| The study's Level 1 DFD splits two jobs that are easy to conflate:
|
|   "The Health Worker module is responsible for patient registration ...
|    The midwife can also create patient accounts, which are stored in the
|    Account Database."
|
| So a health worker records who the patient is; only a midwife decides
| whether that patient may log in. These tests hold that line.
|
*/

use App\Enums\UserRole;
use App\Livewire\PatientRegistry\Record;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('lets a midwife create a portal account for a patient', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create(['user_id' => null]);

    Livewire::actingAs($midwife)
        ->test(Record::class, ['patient' => $patient])
        ->set('portalEmail', 'juan.cruz@example.test')
        ->call('createPortalAccount')
        ->assertHasNoErrors();

    $patient->refresh();

    expect($patient->user)->not->toBeNull()
        ->and($patient->user->email)->toBe('juan.cruz@example.test')
        ->and($patient->user->role)->toBe(UserRole::Patient);
});

it('never stores a password staff could know', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create(['user_id' => null]);

    Livewire::actingAs($midwife)
        ->test(Record::class, ['patient' => $patient])
        ->set('portalEmail', 'nobody.knows@example.test')
        ->call('createPortalAccount');

    $user = $patient->refresh()->user;

    // The placeholder is random, so none of the obvious guesses open it. The
    // patient sets a real one through the password reset flow.
    foreach (['password', 'nobody.knows@example.test', ''] as $guess) {
        expect(Hash::check($guess, $user->password))->toBeFalse();
    }
});

it('stops a health worker from creating a portal account', function () {
    $healthWorker = User::factory()->healthWorker()->create();
    $patient = Patient::factory()->create(['user_id' => null]);

    Livewire::actingAs($healthWorker)
        ->test(Record::class, ['patient' => $patient])
        ->set('portalEmail', 'sneaky@example.test')
        ->call('createPortalAccount')
        ->assertForbidden();

    expect($patient->refresh()->user_id)->toBeNull();
});

it('hides the create button from a health worker', function () {
    $healthWorker = User::factory()->healthWorker()->create();
    $patient = Patient::factory()->create(['user_id' => null]);

    Livewire::actingAs($healthWorker)
        ->test(Record::class, ['patient' => $patient])
        ->assertOk()
        ->assertSee('Only the midwife can create one')
        ->assertDontSee('Create account');
});

it('rejects an email already in use', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create(['user_id' => null]);
    User::factory()->create(['email' => 'taken@example.test']);

    Livewire::actingAs($midwife)
        ->test(Record::class, ['patient' => $patient])
        ->set('portalEmail', 'taken@example.test')
        ->call('createPortalAccount')
        ->assertHasErrors(['portalEmail']);

    expect($patient->refresh()->user_id)->toBeNull();
});

it('requires a valid email address', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create(['user_id' => null]);

    Livewire::actingAs($midwife)
        ->test(Record::class, ['patient' => $patient])
        ->set('portalEmail', 'not-an-email')
        ->call('createPortalAccount')
        ->assertHasErrors(['portalEmail']);

    expect(User::where('role', UserRole::Patient)->count())->toBe(0);
});

it('will not issue a second login to the same patient', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->withLogin()->create();
    $originalUserId = $patient->user_id;

    Livewire::actingAs($midwife)
        ->test(Record::class, ['patient' => $patient])
        ->set('portalEmail', 'second@example.test')
        ->call('createPortalAccount');

    expect($patient->refresh()->user_id)->toBe($originalUserId)
        ->and(User::where('email', 'second@example.test')->exists())->toBeFalse();
});

it('shows the existing login instead of the form', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->withLogin()->create();

    Livewire::actingAs($midwife)
        ->test(Record::class, ['patient' => $patient])
        ->assertOk()
        ->assertSee($patient->user->email)
        ->assertDontSee('Create account');
});
