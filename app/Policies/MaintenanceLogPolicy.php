<?php

namespace App\Policies;

use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MaintenanceLogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-maintenance') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->id === $maintenanceLog->assigned_to 
            || $user->id === $maintenanceLog->created_by
            || $user->can('view-maintenance') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage-maintenance') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->id === $maintenanceLog->assigned_to 
            || $user->can('manage-maintenance') 
            || $user->can('super-admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->can('manage-maintenance') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->can('manage-maintenance') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->can('super-admin');
    }

    /**
     * Determine whether the user can assign maintenance logs.
     */
    public function assign(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->can('manage-maintenance') || $user->can('super-admin');
    }
}
