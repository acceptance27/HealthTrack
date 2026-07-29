<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A person registered at the health centre.
 *
 * This -- not User -- is the patient. Every clinical record points at
 * patients.id. The optional user_id is only the portal login, so a health
 * worker can register a walk-in patient who has no email address yet.
 */
class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'birthdate',
        'contact_number',
        'address',
        'philhealth_number',
        'emergency_contact_name',
        'emergency_contact_number',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }

    /** The portal login, if this patient has one. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function labValues(): HasMany
    {
        return $this->hasMany(LabValue::class);
    }

    public function doctorNotes(): HasMany
    {
        return $this->hasMany(DoctorNote::class);
    }

    public function medicalHistories(): HasMany
    {
        return $this->hasMany(MedicalHistory::class);
    }

    public function allergies(): HasMany
    {
        return $this->hasMany(MedicationAllergy::class);
    }

    /** "Dela Cruz, Juan Miguel" */
    public function fullName(): string
    {
        return trim(sprintf(
            '%s, %s %s',
            $this->last_name,
            $this->first_name,
            $this->middle_name ?? ''
        ));
    }

    public function age(): int
    {
        return $this->birthdate->age;
    }

    /**
     * Free-text search across name and contact number.
     *
     * Lower-cased on both sides rather than using ILIKE, because ILIKE is
     * PostgreSQL-only and the test suite runs on SQLite.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $like = '%'.mb_strtolower($term).'%';

        return $query->where(function (Builder $q) use ($like): void {
            foreach (['first_name', 'middle_name', 'last_name', 'contact_number'] as $column) {
                $q->orWhereRaw("lower({$column}) like ?", [$like]);
            }
        });
    }
}
