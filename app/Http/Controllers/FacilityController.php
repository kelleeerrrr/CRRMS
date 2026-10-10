<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Facility::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $facilities = $query->orderBy('name')->paginate(12);

        return view('facilities.index', compact('facilities', 'search', 'status'));
    }

    public function create()
    {
        return view('facilities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Basic Information
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'location_address' => 'nullable|string',
            
            // Capacity & Amenities
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            
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
            
            // Management Information
            'person_in_charge' => 'nullable|string|max:255',
            'contact_information' => 'nullable|string|max:255',
            'special_instructions' => 'nullable|string',
        ]);

        Facility::create($validated);

        return redirect()->route('facilities.index')
            ->with('success', 'Facility created successfully.');
    }
}
