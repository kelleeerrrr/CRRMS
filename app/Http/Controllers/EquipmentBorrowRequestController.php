<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipmentBorrowRequest;
use App\Http\Requests\UpdateEquipmentBorrowRequest;
use App\Models\EquipmentBorrowRequest;
use App\Services\EquipmentBorrowRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentBorrowRequestController extends Controller
{
    protected EquipmentBorrowRequestService $borrowService;

    public function __construct(EquipmentBorrowRequestService $borrowService)
    {
        $this->borrowService = $borrowService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', EquipmentBorrowRequest::class);

        $filters = $request->only(['status', 'date_from', 'date_to']);
        
        if (Auth::user()->can('manage-equipment')) {
            $requests = $this->borrowService->getAllBorrowRequests($filters);
        } else {
            $requests = $this->borrowService->getUserBorrowRequests(Auth::user(), $filters['status'] ?? null);
        }

        return view('equipment.borrow-requests.index', compact('requests'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', EquipmentBorrowRequest::class);

        $equipmentId = $request->query('equipment_id');
        $equipment = $this->borrowService->getAvailableEquipment(now(), now()->addDays(30));

        return view('equipment.borrow-requests.create', compact('equipment', 'equipmentId'));
    }

    public function store(StoreEquipmentBorrowRequest $request)
    {
        $this->authorize('create', EquipmentBorrowRequest::class);

        try {
            $borrowRequest = $this->borrowService->createBorrowRequest(
                $request->validated(),
                Auth::user()
            );

            return redirect()
                ->route('equipment.borrow-requests.show', $borrowRequest)
                ->with('success', 'Borrow request created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(EquipmentBorrowRequest $borrowRequest)
    {
        $this->authorize('view', $borrowRequest);

        $borrowRequest->load(['items.equipment', 'user', 'organization', 'approvedBy', 'cancelledBy', 'checkedOutBy', 'returnedBy']);

        return view('equipment.borrow-requests.show', compact('borrowRequest'));
    }

    public function edit(EquipmentBorrowRequest $borrowRequest)
    {
        $this->authorize('update', $borrowRequest);

        if (!in_array($borrowRequest->status, ['pending'])) {
            return back()->with('error', 'Only pending requests can be edited.');
        }

        $borrowRequest->load(['items.equipment', 'organization']);
        $equipment = $this->borrowService->getAvailableEquipment(now(), now()->addDays(30));

        return view('equipment.borrow-requests.edit', compact('borrowRequest', 'equipment'));
    }

    public function update(UpdateEquipmentBorrowRequest $request, EquipmentBorrowRequest $borrowRequest)
    {
        $this->authorize('update', $borrowRequest);

        try {
            $borrowRequest = $this->borrowService->updateBorrowRequest(
                $borrowRequest,
                $request->validated()
            );

            return redirect()
                ->route('equipment.borrow-requests.show', $borrowRequest)
                ->with('success', 'Borrow request updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(EquipmentBorrowRequest $borrowRequest)
    {
        $this->authorize('delete', $borrowRequest);

        try {
            $this->borrowService->cancelBorrowRequest($borrowRequest, Auth::user());

            return redirect()
                ->route('equipment.borrow-requests.index')
                ->with('success', 'Borrow request cancelled successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, EquipmentBorrowRequest $borrowRequest)
    {
        $this->authorize('approve', $borrowRequest);

        try {
            $borrowRequest = $this->borrowService->approveBorrowRequest(
                $borrowRequest,
                Auth::user()
            );

            return redirect()
                ->route('equipment.borrow-requests.show', $borrowRequest)
                ->with('success', 'Borrow request approved successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, EquipmentBorrowRequest $borrowRequest)
    {
        $this->authorize('approve', $borrowRequest);

        $request->validate([
            'reason' => ['required', 'string'],
        ]);

        try {
            $borrowRequest = $this->borrowService->rejectBorrowRequest(
                $borrowRequest,
                Auth::user(),
                $request->reason
            );

            return redirect()
                ->route('equipment.borrow-requests.show', $borrowRequest)
                ->with('success', 'Borrow request rejected successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function checkOut(Request $request, EquipmentBorrowRequest $borrowRequest)
    {
        $this->authorize('checkOut', $borrowRequest);

        try {
            $borrowRequest = $this->borrowService->checkOutEquipment(
                $borrowRequest,
                Auth::user()
            );

            return redirect()
                ->route('equipment.borrow-requests.show', $borrowRequest)
                ->with('success', 'Equipment checked out successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function return(Request $request, EquipmentBorrowRequest $borrowRequest)
    {
        $this->authorize('return', $borrowRequest);

        $request->validate([
            'items' => ['required', 'array'],
            'items.*.equipment_id' => ['required', 'exists:equipment,id'],
            'items.*.returned_quantity' => ['required', 'integer', 'min:0'],
            'items.*.condition' => ['required', 'in:good,damaged,lost'],
        ]);

        try {
            $borrowRequest = $this->borrowService->returnEquipment(
                $borrowRequest,
                $request->only('items'),
                Auth::user()
            );

            return redirect()
                ->route('equipment.borrow-requests.show', $borrowRequest)
                ->with('success', 'Equipment returned successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
