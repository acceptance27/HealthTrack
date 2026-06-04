<?php

namespace App\Policies;

use App\Models\PatientProfile;
use App\Models\User;
use App\Policies\Concerns\ChecksBarangayAccess;

class PatientProfilePolicy
{
    use ChecksBarangayAccess;

    public function view(User $user, PatientProfile $patientProfile): bool
    {
        return $user->isAdmin()
            || ($user->isPatient() && $patientProfile->user_id === $user->id)
            || ($user->isMidwife() && $patientProfile->barangay_id === $user->barangay_id);
    }

    public function update(User $user, PatientProfile $patientProfile): bool
    {
        return $this->sameBarangay($user, $patientProfile);
    }
}
