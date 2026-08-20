<?php

/*
|--------------------------------------------------------------------------
| Patient list
|--------------------------------------------------------------------------
*/

use App\Livewire\PatientRegistry\Index;
use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;

it('finds a patient by surname', function () {
    $staff = User::factory()->healthWorker()->create();

    Patient::factory()->create(['first_name' => 'Maria', 'last_name' => 'Santos']);
    Patient::factory()->create(['first_name' => 'Jose', 'last_name' => 'Rizal']);

    Livewire::actingAs($staff)
        ->test(Index::class)
        ->set('search', 'Santos')
        ->assertSee('Santos, Maria')
        ->assertDontSee('Rizal, Jose');
});

it('searches without regard to capitalisation', function () {
    // Guards against reintroducing a case-sensitive LIKE, which behaves
    // differently on PostgreSQL than on the SQLite used by these tests.
    $staff = User::factory()->healthWorker()->create();

    Patient::factory()->create(['first_name' => 'Maria', 'last_name' => 'Santos']);

    Livewire::actingAs($staff)
        ->test(Index::class)
        ->set('search', 'sAnToS')
        ->assertSee('Santos, Maria');
});

it('finds a patient by contact number', function () {
    $staff = User::factory()->healthWorker()->create();

    Patient::factory()->create([
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'contact_number' => '0917 555 1234',
    ]);

    Livewire::actingAs($staff)
        ->test(Index::class)
        ->set('search', '555 1234')
        ->assertSee('Santos, Maria');
});

it('reports when nothing matches', function () {
    $staff = User::factory()->healthWorker()->create();

    Patient::factory()->create(['last_name' => 'Santos']);

    Livewire::actingAs($staff)
        ->test(Index::class)
        ->set('search', 'Nonexistent')
        ->assertSee('No patient matches');
});
