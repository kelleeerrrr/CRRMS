<?php

namespace App\Http\Controllers;

use App\Models\EquipmentBorrowRequest;
use App\Models\FacilityReservation;
use App\Models\ResourceBorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BorrowerRequestController extends Controller
{
    public function index(Request $request, string $category): View
    {
        $approved = $request->routeIs('borrower.approved');

        [$title, $query, $requestRoute, $browseRoute, $searchColumn, $relation, $approvedStatuses] = match ($category) {
            'facilities' => [
                'Facility Reservations', FacilityReservation::with('facility'),
                'facilities.reservations', 'facilities.index', 'title', 'facility',
                ['approved', 'reserved', 'checked_in', 'completed'],
            ],
            'equipment' => [
                'Equipment Borrow Requests', EquipmentBorrowRequest::with('items.equipment'),
                'equipment.borrow-requests', 'equipment.index', 'purpose', 'items.equipment',
                ['approved', 'in_use', 'returned', 'completed'],
            ],
            'resources' => [
                'Resource Requests', ResourceBorrowRequest::with('resource'),
                'resource-borrow-requests', 'resources.index', 'purpose', 'resource',
                ['approved', 'in_use', 'returned'],
            ],
            default => abort(404),
        };

        $search = trim((string) $request->string('search'));
        $query->where('user_id', $request->user()->id);

        if ($approved) {
            $query->whereIn('status', $approvedStatuses);
        }

        if ($search !== '') {
            $query->where(function ($query) use ($search, $searchColumn, $relation) {
                $query->whereLike($searchColumn, "%{$search}%")
                    ->orWhereHas($relation, fn ($related) => $related->whereLike('name', "%{$search}%"));

                if (Str::isUuid($search)) {
                    $query->orWhere('id', $search);
                }
            });
        }

        $records = $query->latest()->orderBy('id')->paginate(15)->appends(['search' => $search]);

        return view('borrower.requests', compact(
            'category', 'title', 'approved', 'records', 'search', 'requestRoute', 'browseRoute'
        ));
    }
}