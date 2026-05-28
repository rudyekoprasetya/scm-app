<?php

namespace App\Policies;

use App\Models\User;

class StockMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-stock');
    }

    public function createIn(User $user): bool
    {
        return $user->can('create-stock-in');
    }

    public function createOut(User $user): bool
    {
        return $user->can('create-stock-out');
    }
}
