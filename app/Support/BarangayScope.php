<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BarangayScope
{
    public static function apply(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where($query->getModel()->getTable().'.barangay_id', $user->barangay_id);
    }
}
