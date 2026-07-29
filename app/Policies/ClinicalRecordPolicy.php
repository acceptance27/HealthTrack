<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * One policy covering all five clinical record models.
 *
 * Because the models live in different classes, auto-discovery will not find
 * this by name -- it is mapped explicitly in AppServiceProvider::boot().
 *
 * The rule from the study: the midwife owns clinical documentation. Health
 * workers register patients but do not diagnose. Patients read, never write.
 */
class ClinicalRecordPolicy
{
    /** Staff browse any patient's records; a patient reads only their own. */
    public function view(User $user, Model $record): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return $record->patient?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isMidwife();
    }

    public function update(User $user, Model $record): bool
    {
        return $user->isMidwife();
    }

    public function delete(User $user, Model $record): bool
    {
        return $user->isMidwife();
    }
}
