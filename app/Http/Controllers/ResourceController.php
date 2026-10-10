<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceBorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    public function index()
    {
        // $this->authorize('viewAny', Resource::class);

        $resources = Resource::with('owner')
            ->latest()
            ->paginate(12);

        return view('resources.index', compact('resources'));
    }

    public function create()
    {
        // $this->authorize('create', Resource::class);

        $buildings = \App\Models\Building::all();
        $rooms = \App\Models\Room::all();
        return view('resources.create', compact('buildings', 'rooms'));
    }

    public function store(Request $request)
    {
        // $this->authorize('create', Resource::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'total_quantity' => 'required|integer|min:1',
            'condition' => 'required|in:New,Good,Fair,Poor,Damaged',
            'status' => 'required|in:available,unavailable',
            'available_for_borrowing' => 'required|boolean',
            'max_borrowing_duration' => 'nullable|integer|min:1',
            'pickup_location' => 'nullable|string|max:255',
            'borrowing_restrictions' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'additional_notes' => 'nullable|string',
            'photo' => 'nullable|image|max:10240',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('resource-photos', 'public');
        }

        // Convert boolean
        $validated['available_for_borrowing'] = $request->has('available_for_borrowing');

        // The creator is always the owner
        $validated['owner_type'] = 'user';
        $validated['owner_id'] = Auth::id();

        Resource::create($validated);

        return redirect()->route('resources.index')
            ->with('success', 'Resource created successfully!');
    }

    public function show(Resource $resource)
    {
        // $this->authorize('view', $resource);

        $resource->load(['owner', 'building', 'room']);

        return view('resources.show', compact('resource'));
    }

    public function edit(Resource $resource)
    {
        // $this->authorize('update', $resource);

        return view('resources.edit', compact('resource'));
    }

    public function update(Request $request, Resource $resource)
    {
        // $this->authorize('update', $resource);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'condition' => 'required|in:excellent,good,fair,poor,damaged',
            'status' => 'required|in:available,borrowed,unavailable,maintenance',
            'lending_conditions' => 'nullable|string',
            'building_id' => 'nullable|uuid|exists:buildings,id',
            'room_id' => 'nullable|uuid|exists:rooms,id',
        ]);

        $resource->update($validated);

        return redirect()->route('resources.my-lending')
            ->with('success', 'Resource updated successfully!');
    }

    public function destroy(Resource $resource)
    {
        $this->authorize('delete', $resource);

        $resource->delete();

        return redirect()->route('resources.my-lending')
            ->with('success', 'Resource deleted successfully!');
    }

    public function myLending()
    {
        $resources = Resource::where('owner_type', 'user')
            ->where('owner_id', Auth::id())
            ->with('borrowRequests')
            ->latest()
            ->paginate(12);

        return view('resources.my-lending', compact('resources'));
    }

    public function myBorrowing()
    {
        $borrowRequests = ResourceBorrowRequest::where('user_id', Auth::id())
            ->with(['resource.owner'])
            ->latest()
            ->paginate(12);

        return view('resources.my-borrowing', compact('borrowRequests'));
    }

    public function storeBorrowRequest(Request $request)
    {
        $validated = $request->validate([
            'resource_id' => 'required|uuid|exists:resources,id',
            'borrower_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email',
            'quantity' => 'required|integer|min:1',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'purpose' => 'required|string|max:500',
            'intended_use' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'agreement' => 'required|accepted',
        ]);

        $resource = Resource::findOrFail($validated['resource_id']);

        if ($resource->status !== 'available') {
            return back()->with('error', 'This resource is not available for borrowing.');
        }

        if ($resource->owner_id === Auth::id()) {
            return back()->with('error', 'You cannot borrow your own resource.');
        }

        if ($validated['quantity'] > $resource->available_quantity) {
            return back()->with('error', 'Requested quantity exceeds available quantity.');
        }

        ResourceBorrowRequest::create([
            'resource_id' => $validated['resource_id'],
            'user_id' => Auth::id(),
            'borrower_name' => $validated['borrower_name'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'],
            'quantity' => $validated['quantity'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'purpose' => $validated['purpose'],
            'intended_use' => $validated['intended_use'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('resources.my-borrowing')
            ->with('success', 'Borrow request submitted successfully!');
    }

    public function showBorrowRequest(ResourceBorrowRequest $request)
    {
        $request->load(['resource', 'user']);
        return view('resource-borrow-requests.show', compact('request'));
    }

    public function editBorrowRequest(ResourceBorrowRequest $request)
    {
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }

        if ($request->status !== 'pending') {
            return back()->with('error', 'Cannot edit a request that is not pending.');
        }

        $request->load(['resource', 'user']);
        return view('resource-borrow-requests.edit', compact('request'));
    }

    public function updateBorrowRequest(Request $request, ResourceBorrowRequest $borrowRequest)
    {
        if ($borrowRequest->user_id !== Auth::id()) {
            abort(403);
        }

        if ($borrowRequest->status !== 'pending') {
            return back()->with('error', 'Cannot update a request that is not pending.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $borrowRequest->resource->available_quantity,
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'purpose' => 'required|string|max:500',
            'intended_use' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email',
        ]);

        $borrowRequest->update($validated);

        return redirect()->route('resource-borrow-requests.show', $borrowRequest)
            ->with('success', 'Borrow request updated successfully!');
    }

    public function destroyBorrowRequest(ResourceBorrowRequest $request)
    {
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }

        if ($request->status !== 'pending') {
            return back()->with('error', 'Cannot cancel a request that is not pending.');
        }

        $request->delete();

        return redirect()->route('resources.my-borrowing')
            ->with('success', 'Borrow request cancelled successfully!');
    }
}

