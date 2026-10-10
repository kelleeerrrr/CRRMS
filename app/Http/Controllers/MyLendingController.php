<?php

namespace App\Http\Controllers;

use App\Models\FacilityReservation;
use App\Models\EquipmentBorrowRequest;
use App\Models\ResourceBorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyLendingController extends Controller
{
    public function requests()
    {
        $user = Auth::user();

        // Get pending facility reservations
        $facilityReservations = FacilityReservation::with('facility')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get pending equipment borrow requests
        $equipmentRequests = EquipmentBorrowRequest::with('items.equipment')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get pending resource borrow requests
        $resourceRequests = ResourceBorrowRequest::with('resource.owner')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('my-lending.requests', compact(
            'facilityReservations',
            'equipmentRequests',
            'resourceRequests'
        ));
    }

    public function borrowing()
    {
        $user = Auth::user();

        // Get approved facility reservations
        $facilityReservations = FacilityReservation::with('facility')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('start_date', 'desc')
            ->get();

        // Get approved equipment borrow requests
        $equipmentRequests = EquipmentBorrowRequest::with('items.equipment')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get approved resource borrow requests
        $resourceRequests = ResourceBorrowRequest::with('resource.owner')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('my-lending.borrowing', compact(
            'facilityReservations',
            'equipmentRequests',
            'resourceRequests'
        ));
    }
}
