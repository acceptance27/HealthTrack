<?php

namespace App\Policies;

use App\Models\MedicationAllergy;
use App\Models\User;
use App\Policies\Concerns\ChecksBarangayAccess;

class MedicationAllergyPolicy
{
    use ChecksBarangayAccess;

    public function view(User $user, MedicationAllergy $medicationAllergy): bool
    {
        return $this->ownsPatientRecord($user, $medicationAllergy) || $this->sameBarangay($user, $medicationAllergy);
    }

    public function update(User $user, MedicationAllergy $medicationAllergy): bool
    {
        return $this->sameBarangay($user, $medicationAllergy);
    }
}
