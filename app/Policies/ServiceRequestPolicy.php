<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServiceRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-service-requests') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->id === $serviceRequest->requested_by 
            || $user->id === $serviceRequest->assigned_to
            || $user->can('view-service-requests') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('view-service-requests') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->id === $serviceRequest->requested_by 
            || $user->id === $serviceRequest->assigned_to
            || $user->can('manage-service-requests') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->id === $serviceRequest->requested_by 
            || $user->can('manage-service-requests') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->can('manage-service-requests') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->can('super-admin');
    }

    /**
     * Determine whether the user can assign service requests.
     */
    public function assign(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->can('manage-service-requests') || $user->can('super-admin');
    }
}
