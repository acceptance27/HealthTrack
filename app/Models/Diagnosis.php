<?php

namespace App\Models;

use App\Models\Concerns\IsClinicalRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory;
    use IsClinicalRecord;

    /** Laravel would guess "diagnosis" from the class name. */
    protected $table = 'diagnoses';

    protected $fillable = [
        'patient_id',
        'created_by',
        'diagnosis',
        'description',
        'diagnosed_at',
    ];

    protected function casts(): array
    {
        return ['diagnosed_at' => 'date'];
    }
}
