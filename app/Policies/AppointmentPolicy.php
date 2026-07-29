<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->isStaff() || $appointment->patient?->user_id === $user->id;
    }

    /**
     * Scheduling is the midwife's responsibility. Patients cannot book --
     * the study describes patient access as read-only.
     */
    public function create(User $user): bool
    {
        return $user->isMidwife();
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->isMidwife();
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->isMidwife();
    }
}
