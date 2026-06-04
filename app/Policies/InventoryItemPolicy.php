<?php

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;
use App\Policies\Concerns\ChecksBarangayAccess;

class InventoryItemPolicy
{
    use ChecksBarangayAccess;

    public function view(User $user, InventoryItem $inventoryItem): bool
    {
        return $this->sameBarangay($user, $inventoryItem);
    }

    public function update(User $user, InventoryItem $inventoryItem): bool
    {
        return $this->sameBarangay($user, $inventoryItem);
    }
}
