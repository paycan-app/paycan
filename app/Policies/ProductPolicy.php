<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authenticatable $user): bool
    {
        // Both AdminUser and User can view product lists
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authenticatable $user, Product $product): bool
    {
        // AdminUser can view all products
        if ($user instanceof AdminUser) {
            return true;
        }

        // User can only view active products
        if ($user instanceof User) {
            return $product->is_active;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authenticatable $user): bool
    {
        // Only AdminUser can create products
        return $user instanceof AdminUser;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user, Product $product): bool
    {
        // Only AdminUser can update products
        return $user instanceof AdminUser;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authenticatable $user, Product $product): bool
    {
        // Only AdminUser can delete products
        return $user instanceof AdminUser;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Authenticatable $user, Product $product): bool
    {
        // Only AdminUser can restore products
        return $user instanceof AdminUser;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Authenticatable $user, Product $product): bool
    {
        // Only AdminUser can permanently delete products
        return $user instanceof AdminUser;
    }
}
