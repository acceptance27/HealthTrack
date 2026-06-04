<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBarangay;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnosis extends Model
{
    use BelongsToBarangay;
    use HasFactory;

    protected $fillable = [
        'barangay_id',
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

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
