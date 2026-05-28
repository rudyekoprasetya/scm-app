<?php

namespace App\Policies;

use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-suppliers');
    }

    public function view(User $user): bool
    {
        return $user->can('view-suppliers');
    }

    public function create(User $user): bool
    {
        return $user->can('create-suppliers');
    }

    public function update(User $user): bool
    {
        return $user->can('edit-suppliers');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete-suppliers');
    }
}
