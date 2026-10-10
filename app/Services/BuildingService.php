<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BuildingService
{
    public function createBuilding(array $data, User $user): Building
    {
        return DB::transaction(function () use ($data, $user) {
            $building = Building::create([
                'id' => Str::uuid(),
                'name' => $data['name'],
                'code' => $data['code'],
                'address' => $data['address'] ?? null,
                'description' => $data['description'] ?? null,
                'total_floors' => $data['total_floors'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
            ]);

            Log::info('Building created', [
                'building_id' => $building->id,
                'user_id' => $user->id,
            ]);

            return $building;
        });
    }

    public function updateBuilding(Building $building, array $data): Building
    {
        return DB::transaction(function () use ($building, $data) {
            $building->update($data);

            Log::info('Building updated', [
                'building_id' => $building->id,
            ]);

            return $building->fresh();
        });
    }

    public function deleteBuilding(Building $building): bool
    {
        return DB::transaction(function () use ($building) {
            // Check if building has any facilities
            if ($building->facilities()->count() > 0) {
                throw new \Exception('Cannot delete building with existing facilities. Please reassign or delete facilities first.');
            }

            // Delete floors and rooms
            foreach ($building->floors as $floor) {
                foreach ($floor->rooms as $room) {
                    $room->delete();
                }
                $floor->delete();
            }

            $building->delete();

            Log::info('Building deleted', [
                'building_id' => $building->id,
            ]);

            return true;
        });
    }

    public function createFloor(array $data, User $user): Floor
    {
        return DB::transaction(function () use ($data, $user) {
            $floor = Floor::create([
                'id' => Str::uuid(),
                'building_id' => $data['building_id'],
                'floor_number' => $data['floor_number'],
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'total_rooms' => $data['total_rooms'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Update building floor count
            $building = Building::findOrFail($data['building_id']);
            $building->update(['total_floors' => $building->floors()->count()]);

            Log::info('Floor created', [
                'floor_id' => $floor->id,
                'building_id' => $data['building_id'],
                'user_id' => $user->id,
            ]);

            return $floor;
        });
    }

    public function updateFloor(Floor $floor, array $data): Floor
    {
        return DB::transaction(function () use ($floor, $data) {
            $floor->update($data);

            Log::info('Floor updated', [
                'floor_id' => $floor->id,
            ]);

            return $floor->fresh();
        });
    }

    public function deleteFloor(Floor $floor): bool
    {
        return DB::transaction(function () use ($floor) {
            // Check if floor has any facilities
            if ($floor->rooms()->whereHas('facilities')->count() > 0) {
                throw new \Exception('Cannot delete floor with existing facilities. Please reassign or delete facilities first.');
            }

            // Delete rooms
            foreach ($floor->rooms as $room) {
                $room->delete();
            }

            $floor->delete();

            // Update building floor count
            $building = $floor->building;
            $building->update(['total_floors' => $building->floors()->count()]);

            Log::info('Floor deleted', [
                'floor_id' => $floor->id,
            ]);

            return true;
        });
    }

    public function createRoom(array $data, User $user): Room
    {
        return DB::transaction(function () use ($data, $user) {
            $room = Room::create([
                'id' => Str::uuid(),
                'floor_id' => $data['floor_id'],
                'room_number' => $data['room_number'],
                'name' => $data['name'] ?? null,
                'type' => $data['type'] ?? 'general',
                'capacity' => $data['capacity'] ?? 0,
                'area' => $data['area'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Update floor room count
            $floor = Floor::findOrFail($data['floor_id']);
            $floor->update(['total_rooms' => $floor->rooms()->count()]);

            Log::info('Room created', [
                'room_id' => $room->id,
                'floor_id' => $data['floor_id'],
                'user_id' => $user->id,
            ]);

            return $room;
        });
    }

    public function updateRoom(Room $room, array $data): Room
    {
        return DB::transaction(function () use ($room, $data) {
            $room->update($data);

            Log::info('Room updated', [
                'room_id' => $room->id,
            ]);

            return $room->fresh();
        });
    }

    public function deleteRoom(Room $room): bool
    {
        return DB::transaction(function () use ($room) {
            // Check if room has any facilities
            if ($room->facilities()->count() > 0) {
                throw new \Exception('Cannot delete room with existing facilities. Please reassign or delete facilities first.');
            }

            $room->delete();

            // Update floor room count
            $floor = $room->floor;
            $floor->update(['total_rooms' => $floor->rooms()->count()]);

            Log::info('Room deleted', [
                'room_id' => $room->id,
            ]);

            return true;
        });
    }

    public function getAllBuildings($filters = [])
    {
        $query = Building::with(['floors.rooms']);

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->get();
    }

    public function getBuildingDirectory($buildingId = null)
    {
        $query = Building::with(['floors.rooms.facilities']);

        if ($buildingId) {
            $query->where('id', $buildingId);
        }

        return $query->orderBy('name')->get();
    }

    public function getBuildingStats(Building $building): array
    {
        return [
            'total_floors' => $building->floors()->count(),
            'total_rooms' => $building->floors()->withCount('rooms')->get()->sum('rooms_count'),
            'total_facilities' => $building->facilities()->count(),
            'active_facilities' => $building->facilities()->where('is_active', true)->count(),
        ];
    }
}
