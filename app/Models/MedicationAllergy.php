<?php

namespace App\Models;

use App\Models\Concerns\IsClinicalRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationAllergy extends Model
{
    use HasFactory;
    use IsClinicalRecord;

    protected $table = 'medication_allergies';

    protected $fillable = [
        'patient_id',
        'created_by',
        'allergen',
        'reaction',
        'severity',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return ['recorded_at' => 'date'];
    }
}
