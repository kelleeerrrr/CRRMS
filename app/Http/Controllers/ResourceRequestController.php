<?php

namespace App\Http\Controllers;

use App\Models\ResourceBorrowRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = ResourceBorrowRequest::with(['resource', 'user']);

        if ($search) {
            $query->whereHas('resource', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('resource-requests.index', compact('requests', 'search', 'status'));
    }

    public function approve(ResourceBorrowRequest $request)
    {
        $request->update(['status' => 'approved']);
        return back()->with('success', 'Resource request approved successfully.');
    }

    public function decline(ResourceBorrowRequest $request)
    {
        $request->update(['status' => 'rejected']);
        return back()->with('success', 'Resource request declined successfully.');
    }
}
