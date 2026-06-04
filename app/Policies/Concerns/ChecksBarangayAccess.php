<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait ChecksBarangayAccess
{
    protected function sameBarangay(User $user, object $record): bool
    {
        return $user->isAdmin()
            || ($user->isMidwife() && $record->barangay_id === $user->barangay_id);
    }

    protected function ownsPatientRecord(User $user, object $record): bool
    {
        return $user->isPatient() && $record->patient_id === $user->id;
    }
}
