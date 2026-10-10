<?php

namespace App\Policies;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ResourcePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_resources');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Resource $resource): bool
    {
        return $user->can('view_resources');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Users can create user-owned resources
        if ($user->can('create_resource_listing')) {
            return true;
        }

        // School Officers can create school-owned resources
        if ($user->can('manage_school_resources')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Resource $resource): bool
    {
        // User can only update their own resources
        if ($resource->isUserOwned() && $resource->owner_id === $user->id) {
            return $user->can('manage_own_resource_listings');
        }

        // School Officer can update school-owned resources
        if ($resource->isSchoolOwned()) {
            return $user->can('manage_school_resources');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Resource $resource): bool
    {
        // User can only delete their own resources
        if ($resource->isUserOwned() && $resource->owner_id === $user->id) {
            return $user->can('manage_own_resource_listings');
        }

        // School Officer can delete school-owned resources
        if ($resource->isSchoolOwned()) {
            return $user->can('manage_school_resources');
        }

        return false;
    }

    /**
     * Determine whether the user can approve requests for the resource.
     */
    public function approveRequests(User $user, Resource $resource): bool
    {
        // Resource owner can approve their own resource requests
        if ($resource->isUserOwned() && $resource->owner_id === $user->id) {
            return $user->can('approve_own_resource_requests');
        }

        // School Officer can approve school-owned resource requests
        if ($resource->isSchoolOwned()) {
            return $user->can('approve_school_resource_requests');
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Resource $resource): bool
    {
        return $this->update($user, $resource);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Resource $resource): bool
    {
        return $this->delete($user, $resource);
    }
}
