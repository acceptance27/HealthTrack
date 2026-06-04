<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBarangay;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalHistory extends Model
{
    use BelongsToBarangay;
    use HasFactory;

    protected $fillable = [
        'barangay_id',
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

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
