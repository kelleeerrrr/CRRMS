<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BuildingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-buildings') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Building $building): bool
    {
        return $user->can('view-buildings') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage-buildings') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Building $building): bool
    {
        return $user->can('manage-buildings') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Building $building): bool
    {
        return $user->can('manage-buildings') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Building $building): bool
    {
        return $user->can('manage-buildings') || $user->can('super-admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Building $building): bool
    {
        return $user->can('super-admin');
    }

    // Floor methods
    public function viewAnyFloor(User $user): bool
    {
        return $user->can('view-buildings') || $user->can('super-admin');
    }

    public function viewFloor(User $user, Floor $floor): bool
    {
        return $user->can('view-buildings') || $user->can('super-admin');
    }

    public function createFloor(User $user): bool
    {
        return $user->can('manage-buildings') || $user->can('super-admin');
    }

    public function updateFloor(User $user, Floor $floor): bool
    {
        return $user->can('manage-buildings') || $user->can('super-admin');
    }

    public function deleteFloor(User $user, Floor $floor): bool
    {
        return $user->can('manage-buildings') || $user->can('super-admin');
    }

    // Room methods
    public function viewAnyRoom(User $user): bool
    {
        return $user->can('view-buildings') || $user->can('super-admin');
    }

    public function viewRoom(User $user, Room $room): bool
    {
        return $user->can('view-buildings') || $user->can('super-admin');
    }

    public function createRoom(User $user): bool
    {
        return $user->can('manage-buildings') || $user->can('super-admin');
    }

    public function updateRoom(User $user, Room $room): bool
    {
        return $user->can('manage-buildings') || $user->can('super-admin');
    }

    public function deleteRoom(User $user, Room $room): bool
    {
        return $user->can('manage-buildings') || $user->can('super-admin');
    }
}
