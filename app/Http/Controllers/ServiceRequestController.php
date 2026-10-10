<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Services\ServiceRequestService;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    protected ServiceRequestService $serviceRequestService;

    public function __construct(ServiceRequestService $serviceRequestService)
    {
        $this->serviceRequestService = $serviceRequestService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['type', 'status', 'priority', 'assigned_to', 'requested_by', 'search']);
        $serviceRequests = $this->serviceRequestService->getServiceRequests($filters);
        $statistics = $this->serviceRequestService->getServiceRequestStatistics();
        $staff = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['staff', 'technician', 'facilities_office']);
        })->get();

        return view('service-requests.index', compact('serviceRequests', 'statistics', 'staff'));
    }

    public function create()
    {
        $staff = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['staff', 'technician', 'facilities_office']);
        })->get();

        return view('service-requests.create', compact('staff'));
    }

    public function store(StoreServiceRequest $request)
    {
        try {
            $serviceRequest = $this->serviceRequestService->createServiceRequest(
                $request->validated(),
                auth()->user()
            );

            return redirect()
                ->route('service-requests.show', $serviceRequest)
                ->with('success', 'Service request created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['requestedBy', 'assignedTo']);

        return view('service-requests.show', compact('serviceRequest'));
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load('requestedBy');
        $staff = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['staff', 'technician', 'facilities_office']);
        })->get();

        return view('service-requests.edit', compact('serviceRequest', 'staff'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'location' => ['nullable', 'string'],
            'priority' => ['sometimes', 'in:low,medium,high,urgent'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'scheduled_date' => ['nullable', 'date'],
        ]);

        try {
            $serviceRequest = $this->serviceRequestService->updateServiceRequest($serviceRequest, $request->validated());

            return redirect()
                ->route('service-requests.show', $serviceRequest)
                ->with('success', 'Service request updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        try {
            $this->serviceRequestService->deleteServiceRequest($serviceRequest);

            return redirect()
                ->route('service-requests.index')
                ->with('success', 'Service request deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function complete(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'completion_notes' => ['nullable', 'string'],
        ]);

        try {
            $serviceRequest = $this->serviceRequestService->completeServiceRequest($serviceRequest, $request->validated());

            return redirect()
                ->route('service-requests.show', $serviceRequest)
                ->with('success', 'Service request marked as completed.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'cancellation_reason' => ['required', 'string'],
        ]);

        try {
            $serviceRequest = $this->serviceRequestService->cancelServiceRequest($serviceRequest, $request->cancellation_reason);

            return redirect()
                ->route('service-requests.show', $serviceRequest)
                ->with('success', 'Service request cancelled successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function assignStaff(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'staff_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $staff = User::findOrFail($request->staff_id);
            $serviceRequest = $this->serviceRequestService->assignStaff($serviceRequest, $staff);

            return redirect()
                ->route('service-requests.show', $serviceRequest)
                ->with('success', 'Staff assigned successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
