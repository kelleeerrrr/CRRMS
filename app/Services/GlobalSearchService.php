<?php

namespace App\Services;

use App\Models\User;
use App\Models\Building;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\FacilityReservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GlobalSearchService
{
    public function search(string $query, array $filters = [])
    {
        $results = [];

        // Search Users
        if (in_array('users', $filters['types'] ?? ['users', 'buildings', 'facilities', 'equipment', 'reservations'])) {
            $results['users'] = $this->searchUsers($query);
        }

        // Search Buildings
        if (in_array('buildings', $filters['types'] ?? ['users', 'buildings', 'facilities', 'equipment', 'reservations'])) {
            $results['buildings'] = $this->searchBuildings($query);
        }

        // Search Facilities
        if (in_array('facilities', $filters['types'] ?? ['users', 'buildings', 'facilities', 'equipment', 'reservations'])) {
            $results['facilities'] = $this->searchFacilities($query);
        }

        // Search Equipment
        if (in_array('equipment', $filters['types'] ?? ['users', 'buildings', 'facilities', 'equipment', 'reservations'])) {
            $results['equipment'] = $this->searchEquipment($query);
        }

        // Search Reservations
        if (in_array('reservations', $filters['types'] ?? ['users', 'buildings', 'facilities', 'equipment', 'reservations'])) {
            $results['reservations'] = $this->searchReservations($query);
        }

        return $results;
    }

    protected function searchUsers(string $query)
    {
        return User::where('name', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->orWhere('student_id', 'like', '%' . $query . '%')
            ->orWhere('employee_id', 'like', '%' . $query . '%')
            ->with('department')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'type' => 'user',
                    'title' => $user->name,
                    'subtitle' => $user->email,
                    'url' => route('users.show', $user),
                    'icon' => 'user',
                ];
            });
    }

    protected function searchBuildings(string $query)
    {
        return Building::where('name', 'like', '%' . $query . '%')
            ->orWhere('code', 'like', '%' . $query . '%')
            ->orWhere('address', 'like', '%' . $query . '%')
            ->limit(10)
            ->get()
            ->map(function ($building) {
                return [
                    'id' => $building->id,
                    'type' => 'building',
                    'title' => $building->name,
                    'subtitle' => $building->code,
                    'url' => route('buildings.show', $building),
                    'icon' => 'building',
                ];
            });
    }

    protected function searchFacilities(string $query)
    {
        return Facility::where('name', 'like', '%' . $query . '%')
            ->orWhere('code', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->with('building')
            ->limit(10)
            ->get()
            ->map(function ($facility) {
                return [
                    'id' => $facility->id,
                    'type' => 'facility',
                    'title' => $facility->name,
                    'subtitle' => $facility->building->name ?? 'N/A',
                    'url' => route('facilities.reservations.show', $facility),
                    'icon' => 'home',
                ];
            });
    }

    protected function searchEquipment(string $query)
    {
        return Equipment::where('name', 'like', '%' . $query . '%')
            ->orWhere('serial_number', 'like', '%' . $query . '%')
            ->orWhere('model', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->limit(10)
            ->get()
            ->map(function ($equipment) {
                return [
                    'id' => $equipment->id,
                    'type' => 'equipment',
                    'title' => $equipment->name,
                    'subtitle' => $equipment->serial_number ?? 'N/A',
                    'url' => route('equipment.borrow-requests.show', $equipment),
                    'icon' => 'cube',
                ];
            });
    }

    protected function searchReservations(string $query)
    {
        return FacilityReservation::where('id', 'like', '%' . $query . '%')
            ->orWhereHas('facility', function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->orWhereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->with(['facility', 'user'])
            ->limit(10)
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'type' => 'reservation',
                    'title' => $reservation->facility->name,
                    'subtitle' => $reservation->user->name,
                    'url' => route('facilities.reservations.show', $reservation),
                    'icon' => 'calendar',
                ];
            });
    }

    public function getRecentSearches(User $user, int $limit = 10)
    {
        // This would typically be stored in a separate table or cache
        // For now, return empty array
        return [];
    }

    public function saveSearch(User $user, string $query): void
    {
        // This would typically be stored in a separate table
        Log::info('Search saved', [
            'user_id' => $user->id,
            'query' => $query,
        ]);
    }
}
