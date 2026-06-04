<?php

use App\Models\User;

it('prevents patients from opening midwife patient management', function () {
    $patient = User::factory()->create();

    $this->actingAs($patient)
        ->get(route('midwife.patients'))
        ->assertForbidden();
});
