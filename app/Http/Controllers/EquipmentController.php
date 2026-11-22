<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $category_id = $request->string('category_id')->toString();
        $status = $request->input('status');

        $query = Equipment::with(['category']);

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category_id) {
            $query->where(Str::isUuid($category_id) ? 'category_id' : 'equipment_category', $category_id);
        }

        if ($status) {
            $query->where('availability_status', match ($status) {
                'available' => 'Available',
                'unavailable' => 'Unavailable',
                'maintenance' => 'Under Maintenance',
                default => $status,
            });
        }

        $equipment = $query->orderBy('name')->paginate(12)->withQueryString();
        $categories = \App\Models\EquipmentCategory::all();

        // Load available_quantity for each equipment item
        $equipment->getCollection()->each(function ($item) {
            $item->available_quantity = $item->getAvailableQuantityAttribute();
        });

        return view('equipment.index', compact('equipment', 'categories', 'search', 'category_id', 'status'));
    }

    public function create()
    {
        return view('equipment.create');
    }

    public function edit(Request $request, Equipment $equipment)
    {
        abort_unless($request->user()->hasRole('school_officer'), 403);

        return view('equipment.create', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        abort_unless($request->user()->hasRole('school_officer'), 403);

        $equipment->update($this->validatedData($request, $equipment));

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment updated successfully.');
    }

    public function store(Request $request)
    {
        Equipment::create($this->validatedData($request));

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment created successfully.');
    }

    private function validatedData(Request $request, ?Equipment $equipment = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_code' => ['required', 'string', 'max:100', $equipment
                ? Rule::unique('equipment', 'equipment_code')->ignore($equipment)
                : Rule::unique('equipment', 'equipment_code')],
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

        unset($validated['equipment_photo']);

        if ($request->hasFile('equipment_photo')) {
            $validated['equipment_photo'] = $request->file('equipment_photo')->store('equipment-photos', 'public');
        }

        return $validated;
    }
}
