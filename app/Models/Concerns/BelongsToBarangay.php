<?php

namespace App\Models\Concerns;

use App\Models\Barangay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBarangay
{
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function scopeForBarangay(Builder $query, int $barangayId): Builder
    {
        return $query->where($query->getModel()->getTable().'.barangay_id', $barangayId);
    }
}
