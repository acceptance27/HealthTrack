<?php

/*
|--------------------------------------------------------------------------
| Patient portal
|--------------------------------------------------------------------------
|
| The study describes patient access as read-only: a patient sees their own
| records, nobody else's, and cannot change anything.
|
*/

use App\Livewire\Shared\ClinicalRecords;
use App\Models\Diagnosis;
use App\Models\Patient;
use Livewire\Livewire;

it('shows a patient their own diagnoses', function () {
    $patient = Patient::factory()->withLogin()->create();

    Diagnosis::factory()->create([
        'patient_id' => $patient->id,
        'diagnosis' => 'Iron deficiency anaemia',
    ]);

    $this->actingAs($patient->user)
        ->get('/patient/my-health-information')
        ->assertOk()
        ->assertSee('Iron deficiency anaemia');
});

it('never shows one patient another patient\'s records', function () {
    $mine = Patient::factory()->withLogin()->create();
    $theirs = Patient::factory()->create();

    Diagnosis::factory()->create([
        'patient_id' => $theirs->id,
        'diagnosis' => 'Confidential other-patient diagnosis',
    ]);

    $this->actingAs($mine->user)
        ->get('/patient/my-health-information')
        ->assertOk()
        ->assertDontSee('Confidential other-patient diagnosis');
});

it('does not offer a patient any way to add records', function () {
    $patient = Patient::factory()->withLogin()->create();

    Livewire::actingAs($patient->user)
        ->test(ClinicalRecords::class, [
            'patient' => $patient,
            'type' => 'diagnoses',
            'readOnly' => true,
        ])
        ->assertDontSee('Add Diagnosis');
});

it('refuses a patient who forces a save through the component', function () {
    $patient = Patient::factory()->withLogin()->create();

    // Bypasses the UI entirely and calls the action directly, which is what
    // an attacker would do. ClinicalRecordPolicy::create must stop it.
    Livewire::actingAs($patient->user)
        ->test(ClinicalRecords::class, [
            'patient' => $patient,
            'type' => 'diagnoses',
        ])
        ->set('form.diagnosis', 'Self-diagnosed')
        ->set('recordDate', now()->format('Y-m-d'))
        ->call('save')
        ->assertForbidden();

    expect(Diagnosis::count())->toBe(0);
});

it('shows an empty state when the account has no patient record', function () {
    $orphan = App\Models\User::factory()->patient()->create();

    $this->actingAs($orphan)
        ->get('/patient/my-health-information')
        ->assertOk()
        ->assertSee('not linked to a patient record');
});
