<?php

namespace App\Models;

use App\Models\Concerns\IsClinicalRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorNote extends Model
{
    use HasFactory;
    use IsClinicalRecord;

    protected $fillable = [
        'patient_id',
        'created_by',
        'title',
        'note',
        'noted_at',
    ];

    protected function casts(): array
    {
        return ['noted_at' => 'date'];
    }
}
