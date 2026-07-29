<?php

namespace App\Models\Concerns;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Shared behaviour for the five clinical record models (diagnoses, lab
 * values, doctor notes, medical history, allergies).
 *
 * They all look the same: they belong to a patient, they remember which
 * staff member entered them, and they carry one date. The fields that
 * differ are declared in config/healthtrack.php.
 */
trait IsClinicalRecord
{
    /** The patient this record describes. */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /** The staff member who entered it. Null if that account was deleted. */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The config key for this model, e.g. "diagnoses".
     *
     * Lets a record find its own definition without hard-coding the key.
     */
    public static function recordType(): string
    {
        foreach (config('healthtrack.records') as $key => $definition) {
            if ($definition['model'] === static::class) {
                return $key;
            }
        }

        throw new \RuntimeException(
            static::class.' is not listed in config/healthtrack.php under "records".'
        );
    }

    /** That model's entry from config/healthtrack.php. */
    public static function recordDefinition(): array
    {
        return config('healthtrack.records.'.static::recordType());
    }
}
