<?php

namespace App\Policies;

use App\Models\FactoryRequest;
use App\Models\User;

class FactoryRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view their own factory requests
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FactoryRequest $factoryRequest): bool
    {
        return $user->id === $factoryRequest->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create factory requests
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FactoryRequest $factoryRequest): bool
    {
        return $user->id === $factoryRequest->user_id && in_array($factoryRequest->status, ['pending', 'quoted']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FactoryRequest $factoryRequest): bool
    {
        return $user->id === $factoryRequest->user_id && in_array($factoryRequest->status, ['pending', 'rejected', 'cancelled']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FactoryRequest $factoryRequest): bool
    {
        return $user->id === $factoryRequest->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FactoryRequest $factoryRequest): bool
    {
        return $user->id === $factoryRequest->user_id;
    }
}
