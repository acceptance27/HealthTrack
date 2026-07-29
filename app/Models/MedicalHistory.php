<?php

namespace App\Models;

use App\Models\Concerns\IsClinicalRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    use HasFactory;
    use IsClinicalRecord;

    /** Laravel would guess "medical_histories" -- which is right, but be explicit. */
    protected $table = 'medical_histories';

    protected $fillable = [
        'patient_id',
        'created_by',
        'condition',
        'details',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return ['recorded_at' => 'date'];
    }
}
