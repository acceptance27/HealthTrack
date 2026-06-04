<?php

use App\Models\Barangay;
use App\Models\PatientProfile;
use App\Models\User;

it('prevents a midwife from viewing a patient in another barangay', function () {
    $barangayA = Barangay::factory()->create();
    $barangayB = Barangay::factory()->create();
    $midwife = User::factory()->midwife()->create(['barangay_id' => $barangayA->id]);
    $patient = PatientProfile::factory()->create(['barangay_id' => $barangayB->id]);

    $this->actingAs($midwife)
        ->get(route('midwife.patients.show', $patient))
        ->assertForbidden();
});
