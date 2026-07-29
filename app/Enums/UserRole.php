<?php

namespace App\Enums;

/**
 * The three roles from the study's module hierarchy chart.
 *
 * Midwife      -- clinical records, appointments, full patient access.
 * HealthWorker -- registers patients and maintains demographics.
 * Patient      -- read-only access to their own records.
 */
enum UserRole: string
{
    case Midwife = 'midwife';
    case HealthWorker = 'health_worker';
    case Patient = 'patient';

    /** Human-readable name for the UI. */
    public function label(): string
    {
        return match ($this) {
            self::Midwife => 'Midwife',
            self::HealthWorker => 'Health Worker',
            self::Patient => 'Patient',
        };
    }

    /** Where this role lands after logging in. */
    public function homeRoute(): string
    {
        return match ($this) {
            self::Midwife => 'midwife.dashboard',
            self::HealthWorker => 'health-worker.dashboard',
            self::Patient => 'patient.dashboard',
        };
    }

    /** Midwives and health workers are staff; patients are not. */
    public function isStaff(): bool
    {
        return $this !== self::Patient;
    }
}
