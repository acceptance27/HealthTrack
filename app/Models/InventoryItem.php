<?php

namespace App\Models;

use App\Enums\InventoryType;
use App\Models\Concerns\BelongsToBarangay;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use BelongsToBarangay;
    use HasFactory;

    protected $fillable = [
        'barangay_id',
        'name',
        'type',
        'description',
        'unit',
        'quantity_on_hand',
        'reorder_level',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => InventoryType::class,
            'quantity_on_hand' => 'integer',
            'reorder_level' => 'integer',
            'expires_at' => 'date',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
