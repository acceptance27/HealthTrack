<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use App\Policies\Concerns\ChecksBarangayAccess;

class AppointmentPolicy
{
    use ChecksBarangayAccess;

    public function view(User $user, Appointment $appointment): bool
    {
        return $this->ownsPatientRecord($user, $appointment) || $this->sameBarangay($user, $appointment);
    }

    public function create(User $user): bool
    {
        return $user->isPatient() || $user->isMidwife() || $user->isAdmin();
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $this->sameBarangay($user, $appointment);
    }
}
