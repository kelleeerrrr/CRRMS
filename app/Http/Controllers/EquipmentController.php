<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $category_id = $request->input('category_id');
        $status = $request->input('status');

        $query = Equipment::with(['category']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        if ($status) {
            $query->where('availability_status', $status);
        }

        $equipment = $query->orderBy('name')->paginate(12);
        $categories = \App\Models\EquipmentCategory::all();

        // Load available_quantity for each equipment item
        $equipment->getCollection()->each(function ($item) {
            $item->available_quantity = $item->getAvailableQuantityAttribute();
        });

        return view('equipment.index', compact('equipment', 'categories', 'search', 'category_id', 'status'));
    }

    public function create()
    {
        $categories = \App\Models\EquipmentCategory::all();
        $buildings = \App\Models\Building::all();
        return view('equipment.create', compact('categories', 'buildings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_code' => 'required|string|max:100|unique:equipment,equipment_code',
            'equipment_category' => 'required|in:Computer,Projector,Audio Equipment,Laboratory Equipment,Sports Equipment,Office Equipment,Other',
            'description' => 'nullable|string',
            'location_building' => 'required|string|max:255',
            'room_storage' => 'nullable|string|max:255',
            'total_quantity' => 'required|integer|min:1',
            'condition' => 'required|in:New,Good,Fair,Poor,Damaged',
            'availability_status' => 'required|in:Available,Unavailable,Under Maintenance,Retired',
            'equipment_photo' => 'nullable|image|max:10240',
            'available_for_borrowing' => 'required|boolean',
            'max_borrowing_duration' => 'nullable|integer|min:1',
            'advance_booking_requirement' => 'nullable|integer|min:0',
            'borrowing_restrictions' => 'nullable|string',
            'responsible_office' => 'nullable|string|max:255',
            'additional_notes' => 'nullable|string',
            // Legacy fields (nullable for backward compatibility)
            'category_id' => 'nullable|uuid|exists:equipment_categories,id',
            'asset_number' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'supplier' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'building_id' => 'nullable|uuid|exists:buildings,id',
            'room_id' => 'nullable|uuid|exists:rooms,id',
            'status' => 'nullable|in:available,borrowed,reserved,maintenance,lost,retired',
            'specifications' => 'nullable|array',
            'photos' => 'nullable|array',
        ]);

        // Handle file upload
        if ($request->hasFile('equipment_photo')) {
            $validated['equipment_photo'] = $request->file('equipment_photo')->store('equipment-photos', 'public');
        }

        // Convert boolean
        $validated['available_for_borrowing'] = $request->has('available_for_borrowing');

        Equipment::create($validated);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment created successfully.');
    }
}
