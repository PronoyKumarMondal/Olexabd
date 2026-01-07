<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $user): bool
    {
        return $user->isAdmin();
    }

    public function view(Admin $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    public function create(Admin $user): bool
    {
        return $user->hasPermission('product_create');
    }

    public function update(Admin $user, Product $product): bool
    {
        return $user->hasPermission('product_edit');
    }

    public function delete(Admin $user, Product $product): bool
    {
        return $user->hasPermission('product_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
}
