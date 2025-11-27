<?php

namespace App\Policies;

use App\Models\MaterialRequest;
use App\Models\User;

class MaterialRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view their own material requests
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MaterialRequest $materialRequest): bool
    {
        return $user->id === $materialRequest->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create material requests
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MaterialRequest $materialRequest): bool
    {
        return $user->id === $materialRequest->user_id && in_array($materialRequest->status, ['pending', 'quoted']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MaterialRequest $materialRequest): bool
    {
        return $user->id === $materialRequest->user_id && in_array($materialRequest->status, ['pending', 'rejected', 'cancelled']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MaterialRequest $materialRequest): bool
    {
        return $user->id === $materialRequest->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MaterialRequest $materialRequest): bool
    {
        return $user->id === $materialRequest->user_id;
    }
}
