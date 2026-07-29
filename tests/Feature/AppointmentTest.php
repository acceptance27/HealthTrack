<?php

/*
|--------------------------------------------------------------------------
| Appointments
|--------------------------------------------------------------------------
|
| Scheduling belongs to the midwife. Patients view their appointments but
| cannot create them.
|
*/

use App\Enums\AppointmentStatus;
use App\Livewire\Midwife\Appointments;
use App\Livewire\Patients\Record;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

it('lets a midwife schedule an appointment from a patient record', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create();

    Livewire::actingAs($midwife)
        ->test(Record::class, ['patient' => $patient])
        ->set('scheduledAt', now()->addWeek()->format('Y-m-d\TH:i'))
        ->set('reason', 'Prenatal check-up')
        ->set('status', 'confirmed')
        ->call('scheduleAppointment')
        ->assertHasNoErrors();

    $appointment = Appointment::sole();

    expect($appointment->patient_id)->toBe($patient->id)
        ->and($appointment->midwife_id)->toBe($midwife->id)
        ->and($appointment->status)->toBe(AppointmentStatus::Confirmed);
});

it('requires a date and a reason', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create();

    Livewire::actingAs($midwife)
        ->test(Record::class, ['patient' => $patient])
        ->call('scheduleAppointment')
        ->assertHasErrors(['scheduledAt', 'reason']);
});

it('stops a health worker from scheduling', function () {
    $healthWorker = User::factory()->healthWorker()->create();
    $patient = Patient::factory()->create();

    Livewire::actingAs($healthWorker)
        ->test(Record::class, ['patient' => $patient])
        ->set('scheduledAt', now()->addWeek()->format('Y-m-d\TH:i'))
        ->set('reason', 'Prenatal check-up')
        ->call('scheduleAppointment')
        ->assertForbidden();

    expect(Appointment::count())->toBe(0);
});

it('lets a midwife change an appointment status', function () {
    $midwife = User::factory()->midwife()->create();
    $appointment = Appointment::factory()->create(['status' => AppointmentStatus::Pending]);

    Livewire::actingAs($midwife)
        ->test(Appointments::class)
        ->call('setStatus', $appointment->id, 'completed');

    expect($appointment->refresh()->status)->toBe(AppointmentStatus::Completed);
});

it('refuses an unknown appointment status', function () {
    $midwife = User::factory()->midwife()->create();
    $appointment = Appointment::factory()->create(['status' => AppointmentStatus::Pending]);

    Livewire::actingAs($midwife)
        ->test(Appointments::class)
        ->call('setStatus', $appointment->id, 'not-a-status')
        ->assertStatus(422);

    expect($appointment->refresh()->status)->toBe(AppointmentStatus::Pending);
});

it('will not delete an appointment belonging to another patient', function () {
    $midwife = User::factory()->midwife()->create();
    $patient = Patient::factory()->create();
    $theirAppointment = Appointment::factory()->create();

    // Scoped by patient before findOrFail -- see the matching test in
    // ClinicalRecordsTest. The appointment must survive.
    expect(fn () => Livewire::actingAs($midwife)
        ->test(Record::class, ['patient' => $patient])
        ->call('deleteAppointment', $theirAppointment->id)
    )->toThrow(ModelNotFoundException::class);

    expect(Appointment::count())->toBe(1);
});
