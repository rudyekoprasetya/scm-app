<?php

namespace App\Policies;

use App\Models\User;

class StockMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-stock');
    }

    public function create(User $user): bool
    {
        return $user->can('create-stock');
    }
}
