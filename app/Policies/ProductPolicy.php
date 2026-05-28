<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-products');
    }

    public function view(User $user): bool
    {
        return $user->can('view-products');
    }

    public function create(User $user): bool
    {
        return $user->can('create-products');
    }

    public function update(User $user): bool
    {
        return $user->can('edit-products');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete-products');
    }
}
