<?php

namespace App\Policies;

use App\Models\User;

class ShipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-shipments');
    }

    public function view(User $user): bool
    {
        return $user->can('view-shipments');
    }

    public function create(User $user): bool
    {
        return $user->can('create-shipments');
    }

    public function update(User $user): bool
    {
        return $user->can('update-shipments');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete-shipments');
    }
}
