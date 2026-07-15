<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authenticatable $user): bool
    {
        // Both AdminUser and User can view orders
        // AdminUser sees all in Filament
        // User sees only own orders (scoped in API controller)
        return $user instanceof AdminUser || $user instanceof User;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authenticatable $user, Order $order): bool
    {
        // AdminUser can view any order
        if ($user instanceof AdminUser) {
            return true;
        }

        // User can only view their own orders
        if ($user instanceof User) {
            return $order->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authenticatable $user): bool
    {
        // Only AdminUser can create orders in Filament
        // Users create orders through API checkout flow
        return $user instanceof AdminUser;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user, Order $order): bool
    {
        // Only AdminUser can update orders
        return $user instanceof AdminUser;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authenticatable $user, Order $order): bool
    {
        // Only AdminUser can delete orders
        return $user instanceof AdminUser;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Authenticatable $user, Order $order): bool
    {
        // Only AdminUser can restore orders
        return $user instanceof AdminUser;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Authenticatable $user, Order $order): bool
    {
        // Only AdminUser can permanently delete orders
        return $user instanceof AdminUser;
    }
}
