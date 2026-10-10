<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityReservationRequest;
use App\Http\Requests\UpdateFacilityReservationRequest;
use App\Models\FacilityReservation;
use App\Services\FacilityReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilityReservationController extends Controller
{
    protected FacilityReservationService $reservationService;

    public function __construct(FacilityReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', FacilityReservation::class);

        $filters = $request->only(['status', 'facility_id', 'date_from', 'date_to']);
        
        if (Auth::user()->can('manage-facilities')) {
            $reservations = $this->reservationService->getAllReservations($filters);
        } else {
            $reservations = $this->reservationService->getUserReservations(Auth::user(), $filters['status'] ?? null);
        }

        return view('facilities.reservations.index', compact('reservations'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', FacilityReservation::class);

        $facilityId = $request->query('facility_id');

        if (!$facilityId) {
            return redirect()->route('facilities.index')->with('error', 'Please select a facility to reserve from the facilities list.');
        }

        return view('facilities.reservations.create', compact('facilityId'));
    }

    public function store(StoreFacilityReservationRequest $request)
    {
        $this->authorize('create', FacilityReservation::class);

        try {
            \Log::info('Reservation store attempt', ['data' => $request->all()]);
            
            $reservation = $this->reservationService->createReservation(
                $request->validated(),
                Auth::user()
            );

            \Log::info('Reservation created successfully', ['reservation_id' => $reservation->id]);

            return redirect()
                ->route('my-lending.requests')
                ->with('success', 'Reservation created successfully.');
        } catch (\Exception $e) {
            \Log::error('Reservation creation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(FacilityReservation $reservation)
    {
        $this->authorize('view', $reservation);

        $reservation->load(['facility', 'user', 'organization', 'approvedBy', 'cancelledBy']);

        return view('facilities.reservations.show', compact('reservation'));
    }

    public function edit(FacilityReservation $reservation)
    {
        $this->authorize('update', $reservation);

        $reservation->load(['facility', 'organization']);

        return view('facilities.reservations.edit', compact('reservation'));
    }

    public function update(UpdateFacilityReservationRequest $request, FacilityReservation $reservation)
    {
        $this->authorize('update', $reservation);

        try {
            $reservation = $this->reservationService->updateReservation(
                $reservation,
                $request->validated()
            );

            return redirect()
                ->route('facilities.reservations.show', $reservation)
                ->with('success', 'Reservation updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(FacilityReservation $reservation)
    {
        $this->authorize('delete', $reservation);

        try {
            $this->reservationService->cancelReservation($reservation, Auth::user());

            return redirect()
                ->route('facilities.reservations.index')
                ->with('success', 'Reservation cancelled successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, FacilityReservation $reservation)
    {
        $this->authorize('approve', $reservation);

        try {
            $reservation = $this->reservationService->approveReservation(
                $reservation,
                Auth::user()
            );

            return redirect()
                ->route('facilities.reservations.show', $reservation)
                ->with('success', 'Reservation approved successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, FacilityReservation $reservation)
    {
        $this->authorize('approve', $reservation);

        $request->validate([
            'reason' => ['required', 'string'],
        ]);

        try {
            $reservation = $this->reservationService->rejectReservation(
                $reservation,
                Auth::user(),
                $request->reason
            );

            return redirect()
                ->route('facilities.reservations.show', $reservation)
                ->with('success', 'Reservation rejected successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function checkIn(Request $request, FacilityReservation $reservation)
    {
        $this->authorize('checkIn', $reservation);

        try {
            $reservation = $this->reservationService->checkInReservation(
                $reservation,
                Auth::user()
            );

            return redirect()
                ->route('facilities.reservations.show', $reservation)
                ->with('success', 'Checked in successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function checkOut(Request $request, FacilityReservation $reservation)
    {
        $this->authorize('checkOut', $reservation);

        try {
            $reservation = $this->reservationService->checkOutReservation(
                $reservation,
                Auth::user()
            );

            return redirect()
                ->route('facilities.reservations.show', $reservation)
                ->with('success', 'Checked out successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function calendar(Request $request)
    {
        $this->authorize('viewAny', FacilityReservation::class);

        $date = $request->get('date', now());
        $reservations = FacilityReservation::with(['facility', 'user'])
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->whereBetween('start_date', [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth()
            ])
            ->get();

        return view('facilities.reservations.calendar', compact('reservations', 'date'));
    }
}
