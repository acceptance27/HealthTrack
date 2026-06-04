<?php

namespace App\Policies;

use App\Models\DoctorNote;
use App\Models\User;
use App\Policies\Concerns\ChecksBarangayAccess;

class DoctorNotePolicy
{
    use ChecksBarangayAccess;

    public function view(User $user, DoctorNote $doctorNote): bool
    {
        return $this->ownsPatientRecord($user, $doctorNote) || $this->sameBarangay($user, $doctorNote);
    }

    public function update(User $user, DoctorNote $doctorNote): bool
    {
        return $this->sameBarangay($user, $doctorNote);
    }
}
