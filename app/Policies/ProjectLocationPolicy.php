<?php

namespace App\Policies;

use App\Models\ProjectLocation;
use App\Models\User;

class ProjectLocationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view their own project locations
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProjectLocation $projectLocation): bool
    {
        return $user->id === $projectLocation->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create project locations
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProjectLocation $projectLocation): bool
    {
        return $user->id === $projectLocation->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProjectLocation $projectLocation): bool
    {
        return $user->id === $projectLocation->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProjectLocation $projectLocation): bool
    {
        return $user->id === $projectLocation->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProjectLocation $projectLocation): bool
    {
        return $user->id === $projectLocation->user_id;
    }
}
