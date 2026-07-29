<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

/**
 * Who may do what to a patient's demographic record.
 *
 * Laravel discovers this automatically: App\Models\Patient -> App\Policies\PatientPolicy.
 * There is no manual registration anywhere.
 */
class PatientPolicy
{
    /** Staff see the patient list; patients do not. */
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    /** Staff see any patient. A patient sees only themselves. */
    public function view(User $user, Patient $patient): bool
    {
        return $user->isStaff() || $patient->user_id === $user->id;
    }

    /** Any staff member may bring a patient record into existence. */
    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    /**
     * Use the patient registration screen.
     *
     * Deliberately narrower than create(): the study assigns patient intake
     * to the health worker, and the route group for
     * /health-worker/register-patient says the same. This ability is what
     * makes the component itself agree, since Livewire methods are reachable
     * without passing through route middleware.
     *
     * To let midwives register patients too, return isStaff() here and widen
     * the route group in routes/web.php to role:midwife,health_worker.
     */
    public function register(User $user): bool
    {
        return $user->isHealthWorker();
    }

    /**
     * Give a patient a portal login.
     *
     * The midwife's job, not the health worker's. The study's Level 1 DFD
     * separates the two: "The Health Worker module is responsible for patient
     * registration ... The midwife can also create patient accounts, which are
     * stored in the Account Database."
     *
     * So a health worker records who the patient is; a midwife decides whether
     * that patient gets to log in. The control lives on the patient record
     * screen rather than the registration form.
     */
    public function createAccount(User $user, Patient $patient): bool
    {
        return $user->isMidwife();
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->isStaff();
    }

    /** Deleting a patient destroys their clinical history -- midwife only. */
    public function delete(User $user, Patient $patient): bool
    {
        return $user->isMidwife();
    }
}
