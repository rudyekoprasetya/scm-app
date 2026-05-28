<?php

namespace App\Policies;

use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-purchase-orders');
    }

    public function view(User $user): bool
    {
        return $user->can('view-purchase-orders');
    }

    public function create(User $user): bool
    {
        return $user->can('create-purchase-orders');
    }

    public function update(User $user): bool
    {
        return $user->can('update-purchase-orders');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete-purchase-orders');
    }
}
