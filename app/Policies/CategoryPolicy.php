<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin $user): bool
    {
        return $user->isAdmin();
    }

    public function view(Admin $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    public function create(Admin $user): bool
    {
        return $user->hasPermission('category_create');
    }

    public function update(Admin $user, Category $category): bool
    {
        return $user->hasPermission('category_edit');
    }

    public function delete(Admin $user, Category $category): bool
    {
        return $user->hasPermission('category_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Category $category): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return false;
    }
}
