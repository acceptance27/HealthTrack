<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBarangay;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientProfile extends Model
{
    use BelongsToBarangay;
    use HasFactory;

    protected $fillable = [
        'barangay_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id', 'user_id');
    }

    public function allergies(): HasMany
    {
        return $this->hasMany(MedicationAllergy::class, 'patient_id', 'user_id');
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class, 'patient_id', 'user_id');
    }

    public function doctorNotes(): HasMany
    {
        return $this->hasMany(DoctorNote::class, 'patient_id', 'user_id');
    }

    public function labValues(): HasMany
    {
        return $this->hasMany(LabValue::class, 'patient_id', 'user_id');
    }

    public function medicalHistories(): HasMany
    {
        return $this->hasMany(MedicalHistory::class, 'patient_id', 'user_id');
    }

    public function scopeWithLastAppointmentDate($query)
    {
        return $query->addSelect([
            'last_appointment_at' => Appointment::select('scheduled_at')
                ->whereColumn('patient_id', 'patient_profiles.user_id')
                ->latest('scheduled_at')
                ->take(1)
        ]);
    }

    public function fullName(): string
    {
        return trim("{$this->last_name}, {$this->first_name} {$this->middle_name}");
    }
}
