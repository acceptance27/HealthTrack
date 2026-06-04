<?php

namespace App\Policies;

use App\Models\Diagnosis;
use App\Models\User;
use App\Policies\Concerns\ChecksBarangayAccess;

class DiagnosisPolicy
{
    use ChecksBarangayAccess;

    public function view(User $user, Diagnosis $diagnosis): bool
    {
        return $this->ownsPatientRecord($user, $diagnosis) || $this->sameBarangay($user, $diagnosis);
    }

    public function update(User $user, Diagnosis $diagnosis): bool
    {
        return $this->sameBarangay($user, $diagnosis);
    }
}
