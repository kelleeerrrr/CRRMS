<?php

namespace App\Http\Controllers;

use App\Models\ResourceBorrowRequest;
use App\Services\ResourceBorrowRequestService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceRequestController extends Controller
{
    protected ResourceBorrowRequestService $borrowRequestService;

    public function __construct(ResourceBorrowRequestService $borrowRequestService)
    {
        $this->borrowRequestService = $borrowRequestService;
    }

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $filters = [];
        if ($search) {
            $filters['search'] = $search;
        }
        if ($status) {
            $filters['status'] = $status;
        }

        $requests = $this->borrowRequestService->getAllBorrowRequests($filters);

        return view('resource-requests.index', compact('requests', 'search', 'status'));
    }

    public function approve(ResourceBorrowRequest $request)
    {
        try {
            $this->borrowRequestService->approveBorrowRequest($request, auth()->user());
            return back()->with('success', 'Resource request approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function decline(Request $httpRequest, ResourceBorrowRequest $request)
    {
        try {
            $reason = $httpRequest->input('reason');
            $this->borrowRequestService->rejectBorrowRequest($request, auth()->user(), $reason);
            return back()->with('success', 'Resource request declined successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function checkOut(ResourceBorrowRequest $request)
    {
        abort_unless($request->canBeApprovedBy(auth()->user()), 403);

        try {
            $this->borrowRequestService->checkOutResource($request, auth()->user());

            return back()->with('success', 'Resource released successfully.');
        } catch (\DomainException $error) {
            return back()->with('error', $error->getMessage());
        }
    }

    public function returnResource(ResourceBorrowRequest $request)
    {
        abort_unless($request->canBeApprovedBy(auth()->user()), 403);

        try {
            $this->borrowRequestService->returnResource($request, auth()->user());

            return back()->with('success', 'Resource return recorded.');
        } catch (\DomainException $error) {
            return back()->with('error', $error->getMessage());
        }
    }
}
