<?php

namespace App\Policies;

use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-categories');
    }

    public function view(User $user): bool
    {
        return $user->can('view-categories');
    }

    public function create(User $user): bool
    {
        return $user->can('create-categories');
    }

    public function update(User $user): bool
    {
        return $user->can('update-categories');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete-categories');
    }
}
