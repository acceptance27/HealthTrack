<?php

/*
|--------------------------------------------------------------------------
| Clinical records
|--------------------------------------------------------------------------
|
| The shared ClinicalRecords component drives all five record types, so these
| tests run against every type declared in config/healthtrack.php. Add a new
| type to that config and it is covered here automatically.
|
*/

use App\Livewire\ClinicalRecords;
use App\Models\Diagnosis;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

it('renders every configured record type without error', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create();

    foreach (array_keys(config('healthtrack.records')) as $type) {
        Livewire::actingAs($midwife)
            ->test(ClinicalRecords::class, ['patient' => $patient, 'type' => $type])
            ->assertOk();
    }
});

it('lets a midwife save a diagnosis', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create();

    Livewire::actingAs($midwife)
        ->test(ClinicalRecords::class, ['patient' => $patient, 'type' => 'diagnoses'])
        ->set('form.diagnosis', 'Hypertension')
        ->set('form.description', 'Stage 1, monitor monthly.')
        ->set('recordDate', '2026-07-01')
        ->call('save')
        ->assertHasNoErrors();

    $diagnosis = Diagnosis::sole();

    expect($diagnosis->diagnosis)->toBe('Hypertension')
        ->and($diagnosis->patient_id)->toBe($patient->id)
        ->and($diagnosis->created_by)->toBe($midwife->id);
});

it('applies the validation rules from the config file', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create();

    Livewire::actingAs($midwife)
        ->test(ClinicalRecords::class, ['patient' => $patient, 'type' => 'diagnoses'])
        ->set('form.diagnosis', '')
        ->set('recordDate', '')
        ->call('save')
        ->assertHasErrors(['form.diagnosis', 'recordDate']);

    expect(Diagnosis::count())->toBe(0);
});

it('rejects a record dated in the future', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create();

    Livewire::actingAs($midwife)
        ->test(ClinicalRecords::class, ['patient' => $patient, 'type' => 'diagnoses'])
        ->set('form.diagnosis', 'Hypertension')
        ->set('recordDate', now()->addWeek()->format('Y-m-d'))
        ->call('save')
        ->assertHasErrors(['recordDate']);
});

it('stops a health worker from writing clinical records', function () {
    // Health workers register patients; they do not diagnose.
    $healthWorker = User::factory()->healthWorker()->create();
    $patient = Patient::factory()->create();

    Livewire::actingAs($healthWorker)
        ->test(ClinicalRecords::class, ['patient' => $patient, 'type' => 'diagnoses'])
        ->set('form.diagnosis', 'Hypertension')
        ->set('recordDate', '2026-07-01')
        ->call('save')
        ->assertForbidden();

    expect(Diagnosis::count())->toBe(0);
});

it('lets a midwife delete a record', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create();
    $diagnosis = Diagnosis::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($midwife)
        ->test(ClinicalRecords::class, ['patient' => $patient, 'type' => 'diagnoses'])
        ->call('delete', $diagnosis->id);

    expect(Diagnosis::count())->toBe(0);
});

it('will not delete a record belonging to a different patient', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();

    $theirDiagnosis = Diagnosis::factory()->create(['patient_id' => $otherPatient->id]);

    // delete() scopes by patient before findOrFail, so another patient's
    // record is simply not found. Over HTTP that surfaces as a 404; called
    // directly, as here, the exception propagates. Either way the record
    // must survive -- that is the part worth asserting.
    expect(fn () => Livewire::actingAs($midwife)
        ->test(ClinicalRecords::class, ['patient' => $patient, 'type' => 'diagnoses'])
        ->call('delete', $theirDiagnosis->id)
    )->toThrow(ModelNotFoundException::class);

    expect(Diagnosis::count())->toBe(1);
});

it('rejects a record type that is not in the config', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create();

    Livewire::actingAs($midwife)
        ->test(ClinicalRecords::class, ['patient' => $patient, 'type' => 'not-a-real-type'])
        ->assertNotFound();
});
