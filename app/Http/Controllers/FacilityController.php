<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Facility::with('room');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $facilities = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('facilities.index', compact('facilities', 'search', 'status'));
    }

    public function create()
    {
        return view('facilities.create');
    }

    public function edit(Request $request, Facility $facility)
    {
        abort_unless($request->user()->hasRole('school_officer'), 403);

        return view('facilities.create', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        abort_unless($request->user()->hasRole('school_officer'), 403);

        $facility->update($this->validatedData($request, $facility));

        return redirect()->route('facilities.index')
            ->with('success', 'Facility updated successfully.');
    }

    public function store(Request $request)
    {
        Facility::create($this->validatedData($request));

        return redirect()->route('facilities.index')
            ->with('success', 'Facility created successfully.');
    }

    private function validatedData(Request $request, ?Facility $facility = null): array
    {
        $validated = $request->validate([
            // Basic Information
            'name' => 'required|string|max:255',
            'type' => 'required|in:Classroom,Laboratory,Auditorium,Conference Room,Function Room,Sports Facility,Other',
            'location_address' => 'nullable|string',
            
            // Capacity & Amenities
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'amenities.*' => 'required|string|max:255',
            
            // Availability
            'status' => 'required|in:available,unavailable,maintenance',
            'operating_days' => 'nullable|string',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            
            // Reservation Settings
            'max_reservation_duration' => 'nullable|integer|min:1',
            'advance_booking_requirement' => 'nullable|integer|min:0',
            'rules' => 'nullable|string',
            
            // Facility Image
            'photos' => 'nullable|array',
            'photos.*' => 'required|image|max:10240',
            
            // Management Information
            'person_in_charge' => 'nullable|string|max:255',
            'contact_information' => 'nullable|string|max:255',
            'special_instructions' => 'nullable|string',
        ]);

        $validated['amenities'] = $validated['amenities'] ?? [];

        unset($validated['photos']);

        if ($request->hasFile('photos')) {
            $validated['photos'] = array_merge(
                $facility?->photos ?? [],
                array_map(fn ($photo) => $photo->store('facility-photos', 'public'), $request->file('photos'))
            );
        }

        return $validated;
    }
}
