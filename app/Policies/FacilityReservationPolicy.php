<?php

namespace App\Policies;

use App\Models\FacilityReservation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FacilityReservationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-facilities') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FacilityReservation $facilityReservation): bool
    {
        return $user->id === $facilityReservation->user_id 
            || $user->can('view-facilities') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('view-facilities') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FacilityReservation $facilityReservation): bool
    {
        return $user->id === $facilityReservation->user_id 
            || $user->can('manage-facilities') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FacilityReservation $facilityReservation): bool
    {
        return $user->id === $facilityReservation->user_id 
            || $user->can('manage-facilities') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FacilityReservation $facilityReservation): bool
    {
        return $user->can('manage-facilities') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FacilityReservation $facilityReservation): bool
    {
        return $user->can('super-admin');
    }

    /**
     * Determine whether the user can approve reservations.
     */
    public function approve(User $user, FacilityReservation $facilityReservation): bool
    {
        return $user->can('approve-reservations') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can check in reservations.
     */
    public function checkIn(User $user, FacilityReservation $facilityReservation): bool
    {
        return $user->can('manage-facilities') 
            || $user->hasRole('security') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can check out reservations.
     */
    public function checkOut(User $user, FacilityReservation $facilityReservation): bool
    {
        return $user->can('manage-facilities') 
            || $user->hasRole('security') 
            || $user->can('super-admin');
    }
}
