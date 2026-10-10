<?php

namespace App\Policies;

use App\Models\EquipmentBorrowRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EquipmentBorrowRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-equipment') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EquipmentBorrowRequest $equipmentBorrowRequest): bool
    {
        return $user->id === $equipmentBorrowRequest->user_id 
            || $user->can('view-equipment') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('view-equipment') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EquipmentBorrowRequest $equipmentBorrowRequest): bool
    {
        return $user->id === $equipmentBorrowRequest->user_id 
            || $user->can('manage-equipment') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EquipmentBorrowRequest $equipmentBorrowRequest): bool
    {
        return $user->id === $equipmentBorrowRequest->user_id 
            || $user->can('manage-equipment') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EquipmentBorrowRequest $equipmentBorrowRequest): bool
    {
        return $user->can('manage-equipment') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EquipmentBorrowRequest $equipmentBorrowRequest): bool
    {
        return $user->can('super-admin');
    }

    /**
     * Determine whether the user can approve borrow requests.
     */
    public function approve(User $user, EquipmentBorrowRequest $equipmentBorrowRequest): bool
    {
        return $user->can('approve-borrow-requests') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can check out equipment.
     */
    public function checkOut(User $user, EquipmentBorrowRequest $equipmentBorrowRequest): bool
    {
        return $user->can('manage-equipment') 
            || $user->hasRole('property_custodian')
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can return equipment.
     */
    public function return(User $user, EquipmentBorrowRequest $equipmentBorrowRequest): bool
    {
        return $user->can('manage-equipment') 
            || $user->hasRole('property_custodian')
            || $user->can('super-admin');
    }
}
