<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSeller() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Service $service): bool
    {
        // Seller can view own services, admin can view all
        return $user->isAdmin() || ($user->isSeller() && $service->user_id === $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSeller();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Service $service): bool
    {
        // Seller can update own services, admin can update all
        return $user->isAdmin() || ($user->isSeller() && $service->user_id === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Service $service): bool
    {
        // Seller can delete own services, admin can delete all
        return $user->isAdmin() || ($user->isSeller() && $service->user_id === $user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can approve the service.
     */
    public function approve(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can reject the service.
     */
    public function reject(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }
}
