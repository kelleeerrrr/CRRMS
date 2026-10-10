<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuilding;
use App\Http\Requests\UpdateBuilding;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use App\Services\BuildingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuildingController extends Controller
{
    protected BuildingService $buildingService;

    public function __construct(BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    // Building CRUD
    public function index(Request $request)
    {
        $this->authorize('viewAny', Building::class);

        $filters = $request->only(['is_active']);
        $buildings = $this->buildingService->getAllBuildings($filters);

        return view('buildings.index', compact('buildings'));
    }

    public function create()
    {
        $this->authorize('create', Building::class);

        return view('buildings.create');
    }

    public function store(StoreBuilding $request)
    {
        $this->authorize('create', Building::class);

        try {
            $building = $this->buildingService->createBuilding(
                $request->validated(),
                Auth::user()
            );

            return redirect()
                ->route('buildings.show', $building)
                ->with('success', 'Building created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Building $building)
    {
        $this->authorize('view', $building);

        $building->load(['floors.rooms.facilities']);
        $stats = $this->buildingService->getBuildingStats($building);

        return view('buildings.show', compact('building', 'stats'));
    }

    public function edit(Building $building)
    {
        $this->authorize('update', $building);

        return view('buildings.edit', compact('building'));
    }

    public function update(UpdateBuilding $request, Building $building)
    {
        $this->authorize('update', $building);

        try {
            $building = $this->buildingService->updateBuilding(
                $building,
                $request->validated()
            );

            return redirect()
                ->route('buildings.show', $building)
                ->with('success', 'Building updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Building $building)
    {
        $this->authorize('delete', $building);

        try {
            $this->buildingService->deleteBuilding($building);

            return redirect()
                ->route('buildings.index')
                ->with('success', 'Building deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    // Floor CRUD
    public function createFloor(Building $building)
    {
        $this->authorize('create', Floor::class);

        return view('buildings.floors.create', compact('building'));
    }

    public function storeFloor(Request $request, Building $building)
    {
        $this->authorize('create', Floor::class);

        $request->validate([
            'floor_number' => ['required', 'integer', 'min:1'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'total_rooms' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        try {
            $floor = $this->buildingService->createFloor(
                array_merge($request->validated(), ['building_id' => $building->id]),
                Auth::user()
            );

            return redirect()
                ->route('buildings.show', $building)
                ->with('success', 'Floor created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function editFloor(Building $building, Floor $floor)
    {
        $this->authorize('update', $floor);

        return view('buildings.floors.edit', compact('building', 'floor'));
    }

    public function updateFloor(Request $request, Building $building, Floor $floor)
    {
        $this->authorize('update', $floor);

        $request->validate([
            'floor_number' => ['sometimes', 'integer', 'min:1'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'total_rooms' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        try {
            $floor = $this->buildingService->updateFloor($floor, $request->validated());

            return redirect()
                ->route('buildings.show', $building)
                ->with('success', 'Floor updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function deleteFloor(Building $building, Floor $floor)
    {
        $this->authorize('delete', $floor);

        try {
            $this->buildingService->deleteFloor($floor);

            return redirect()
                ->route('buildings.show', $building)
                ->with('success', 'Floor deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    // Room CRUD
    public function createRoom(Building $building, Floor $floor)
    {
        $this->authorize('create', Room::class);

        return view('buildings.rooms.create', compact('building', 'floor'));
    }

    public function storeRoom(Request $request, Building $building, Floor $floor)
    {
        $this->authorize('create', Room::class);

        $request->validate([
            'room_number' => ['required', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:255'],
            'type' => ['sometimes', 'in:classroom,office,laboratory,conference_room,auditorium,gym,cafeteria,library,general'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'area' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        try {
            $room = $this->buildingService->createRoom(
                array_merge($request->validated(), ['floor_id' => $floor->id]),
                Auth::user()
            );

            return redirect()
                ->route('buildings.show', $building)
                ->with('success', 'Room created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function editRoom(Building $building, Floor $floor, Room $room)
    {
        $this->authorize('update', $room);

        return view('buildings.rooms.edit', compact('building', 'floor', 'room'));
    }

    public function updateRoom(Request $request, Building $building, Floor $floor, Room $room)
    {
        $this->authorize('update', $room);

        $request->validate([
            'room_number' => ['sometimes', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:255'],
            'type' => ['sometimes', 'in:classroom,office,laboratory,conference_room,auditorium,gym,cafeteria,library,general'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'area' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        try {
            $room = $this->buildingService->updateRoom($room, $request->validated());

            return redirect()
                ->route('buildings.show', $building)
                ->with('success', 'Room updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function deleteRoom(Building $building, Floor $floor, Room $room)
    {
        $this->authorize('delete', $room);

        try {
            $this->buildingService->deleteRoom($room);

            return redirect()
                ->route('buildings.show', $building)
                ->with('success', 'Room deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    // Directory
    public function directory(Request $request)
    {
        $this->authorize('viewAny', Building::class);

        $buildingId = $request->query('building_id');
        $directory = $this->buildingService->getBuildingDirectory($buildingId);

        return view('buildings.directory', compact('directory', 'buildingId'));
    }
}
