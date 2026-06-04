<?php

namespace App\Policies;

use App\Models\MedicalHistory;
use App\Models\User;
use App\Policies\Concerns\ChecksBarangayAccess;

class MedicalHistoryPolicy
{
    use ChecksBarangayAccess;

    public function view(User $user, MedicalHistory $medicalHistory): bool
    {
        return $this->ownsPatientRecord($user, $medicalHistory) || $this->sameBarangay($user, $medicalHistory);
    }

    public function update(User $user, MedicalHistory $medicalHistory): bool
    {
        return $this->sameBarangay($user, $medicalHistory);
    }
}
