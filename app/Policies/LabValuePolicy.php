<?php

namespace App\Policies;

use App\Models\LabValue;
use App\Models\User;
use App\Policies\Concerns\ChecksBarangayAccess;

class LabValuePolicy
{
    use ChecksBarangayAccess;

    public function view(User $user, LabValue $labValue): bool
    {
        return $this->ownsPatientRecord($user, $labValue) || $this->sameBarangay($user, $labValue);
    }

    public function update(User $user, LabValue $labValue): bool
    {
        return $this->sameBarangay($user, $labValue);
    }
}
