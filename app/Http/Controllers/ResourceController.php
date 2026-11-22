<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceBorrowRequest;
use App\Http\Requests\UpdateResourceBorrowRequest;
use App\Models\Resource;
use App\Models\ResourceBorrowRequest;
use App\Services\ResourceBorrowRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    protected ResourceBorrowRequestService $borrowRequestService;

    public function __construct(ResourceBorrowRequestService $borrowRequestService)
    {
        $this->borrowRequestService = $borrowRequestService;
    }

    public function index(Request $request)
    {
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();

        $resources = Resource::with('owner')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('resources.index', compact('resources', 'search', 'status'));
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
            'description' => 'nullable|string',
            'available_quantity' => 'required|integer|min:1',
            'condition' => 'required|in:New,Good,Fair,Poor,Damaged',
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

        // Set total_quantity equal to available_quantity
        $validated['total_quantity'] = $validated['available_quantity'];

        // Always set status to available for new resources
        $validated['status'] = 'available';

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
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'available_quantity' => 'required|integer|min:1',
            'condition' => 'required|in:New,Good,Fair,Poor,Damaged',
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

        // Set total_quantity equal to available_quantity
        $validated['total_quantity'] = $validated['available_quantity'];

        // Keep status as is (don't change it on edit)
        unset($validated['status']);

        $resource->update($validated);

        return redirect()->route('resources.show', $resource)
            ->with('success', 'Resource updated successfully!');
    }

    public function toggleAvailability(Resource $resource)
    {
        // Only the owner can toggle availability
        if ($resource->owner_id !== auth()->id()) {
            abort(403, 'You do not have permission to toggle this resource.');
        }

        $resource->status = $resource->status === 'available' ? 'unavailable' : 'available';
        $resource->save();

        return back()->with('success', 'Resource availability updated successfully!');
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

    public function createBorrowRequest(Request $request)
    {
        $resourceId = $request->query('resource_id');
        $resource = Resource::findOrFail($resourceId);

        return view('resource-borrow-requests.create', compact('resource'));
    }

    public function storeBorrowRequest(StoreResourceBorrowRequest $request)
    {
        try {
            $borrowRequest = $this->borrowRequestService->createBorrowRequest(
                $request->validated(),
                Auth::user()
            );

            return redirect()->route('borrower.requests', ['category' => 'resources'])
                ->with('success', 'Borrow request submitted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showBorrowRequest(ResourceBorrowRequest $request)
    {
        $request->load(['resource', 'user']);

        $user = Auth::user();
        abort_unless($request->user_id === $user->id
            || $request->canBeApprovedBy($user)
            || $user->hasRole('school_officer'), 403);

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

    public function updateBorrowRequest(UpdateResourceBorrowRequest $httpRequest, ResourceBorrowRequest $request)
    {
        try {
            $borrowRequest = $this->borrowRequestService->updateBorrowRequest(
                $request,
                $httpRequest->validated(),
                Auth::user()
            );

            return redirect()->route('resource-borrow-requests.show', $borrowRequest)
                ->with('success', 'Borrow request updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroyBorrowRequest(ResourceBorrowRequest $request)
    {
        try {
            $this->borrowRequestService->cancelBorrowRequest($request, Auth::user());

            return redirect()->route('borrower.requests', ['category' => 'resources'])
                ->with('success', 'Borrow request cancelled successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

