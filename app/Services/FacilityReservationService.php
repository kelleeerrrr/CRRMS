<?php

namespace App\Services;

use App\Events\FacilityReservationStatusChanged;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\FacilitySchedule;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class FacilityReservationService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function createReservation(array $data, User $user): FacilityReservation
    {
        return DB::transaction(function () use ($data, $user) {
            // Check for conflicts if facility_id is provided
            if (isset($data['facility_id'])) {
                $this->checkForConflicts($data['facility_id'], $data['start_date'], $data['end_date'], $data['start_time'], $data['end_time']);
            }

            $reservation = FacilityReservation::create([
                'id' => Str::uuid(),
                'facility_id' => $data['facility_id'] ?? null,
                'user_id' => $user->id,
                'organization_id' => $data['organization_id'] ?? null,
                'title' => $data['reservee_name'] ?? 'Reservation',
                'description' => $data['description'] ?? null,
                'reservee_name' => $data['reservee_name'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'status' => 'pending',
                'qr_code' => null,
            ]);

            // Generate QR code (skip if fails)
            try {
                $reservation->qr_code = $this->generateQRCode($reservation);
                $reservation->save();
            } catch (\Exception $e) {
                Log::error('QR code generation failed', ['error' => $e->getMessage()]);
            }

            // Send notification to admins (skip if fails)
            try {
                $admins = User::whereHas('roles', function($query) {
                    $query->where('name', 'super_admin')
                        ->orWhere('name', 'admin')
                        ->orWhere('name', 'facilities_office')
                        ->orWhere('name', 'property_custodian');
                })->get();
                
                foreach ($admins as $admin) {
                    $this->notificationService->createNotification([
                        'user_id' => $admin->id,
                        'type' => 'facility_reservation_created',
                        'title' => 'New Facility Reservation',
                        'message' => "A new facility reservation has been created by {$user->name}.",
                        'data' => [
                            'reservation_id' => $reservation->id,
                            'facility_id' => $reservation->facility_id,
                            'user_id' => $user->id,
                        ],
                    ], $user);
                }
            } catch (\Exception $e) {
                Log::error('Notification sending failed', ['error' => $e->getMessage()]);
            }

            Log::info('Facility reservation created', [
                'reservation_id' => $reservation->id,
                'user_id' => $user->id,
                'facility_id' => $data['facility_id'] ?? null,
            ]);

            return $reservation;
        });
    }

    public function updateReservation(FacilityReservation $reservation, array $data): FacilityReservation
    {
        return DB::transaction(function () use ($reservation, $data) {
            // Check for conflicts if dates/times changed
            if ($this->hasDateTimeChanged($reservation, $data)) {
                $this->checkForConflicts(
                    $data['facility_id'] ?? $reservation->facility_id,
                    $data['start_date'] ?? $reservation->start_date,
                    $data['end_date'] ?? $reservation->end_date,
                    $data['start_time'] ?? $reservation->start_time,
                    $data['end_time'] ?? $reservation->end_time,
                    $reservation->id
                );
            }

            $reservation->update($data);

            Log::info('Facility reservation updated', [
                'reservation_id' => $reservation->id,
            ]);

            return $reservation->fresh();
        });
    }

    public function approveReservation(FacilityReservation $reservation, User $approver): FacilityReservation
    {
        return DB::transaction(function () use ($reservation, $approver) {
            $reservation->update([
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'approval_notes' => $reservation->approval_notes ?? null,
            ]);

            // Send notification to the requester
            $reservation->user->notify(new FacilityReservationApproved($reservation));

            // Broadcast status change
            broadcast(new FacilityReservationStatusChanged($reservation->fresh()));

            Log::info('Facility reservation approved', [
                'reservation_id' => $reservation->id,
                'approved_by' => $approver->id,
            ]);

            return $reservation->fresh();
        });
    }

    public function rejectReservation(FacilityReservation $reservation, User $approver, string $reason): FacilityReservation
    {
        return DB::transaction(function () use ($reservation, $approver, $reason) {
            $reservation->update([
                'status' => 'rejected',
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'rejection_reason' => $reason,
            ]);

            // Broadcast status change
            broadcast(new FacilityReservationStatusChanged($reservation->fresh()));

            Log::info('Facility reservation rejected', [
                'reservation_id' => $reservation->id,
                'approved_by' => $approver->id,
                'reason' => $reason,
            ]);

            return $reservation->fresh();
        });
    }

    public function cancelReservation(FacilityReservation $reservation, User $user): FacilityReservation
    {
        return DB::transaction(function () use ($reservation, $user) {
            $reservation->update([
                'status' => 'cancelled',
                'cancelled_by' => $user->id,
                'cancelled_at' => now(),
            ]);

            // Broadcast status change
            broadcast(new FacilityReservationStatusChanged($reservation->fresh()));

            Log::info('Facility reservation cancelled', [
                'reservation_id' => $reservation->id,
                'cancelled_by' => $user->id,
            ]);

            return $reservation->fresh();
        });
    }

    public function checkInReservation(FacilityReservation $reservation, User $security): FacilityReservation
    {
        return DB::transaction(function () use ($reservation, $security) {
            $reservation->update([
                'checked_in_at' => now(),
                'checked_in_by' => $security->id,
            ]);

            Log::info('Facility reservation checked in', [
                'reservation_id' => $reservation->id,
                'checked_in_by' => $security->id,
            ]);

            return $reservation->fresh();
        });
    }

    public function checkOutReservation(FacilityReservation $reservation, User $security): FacilityReservation
    {
        return DB::transaction(function () use ($reservation, $security) {
            $reservation->update([
                'checked_out_at' => now(),
                'checked_out_by' => $security->id,
            ]);

            Log::info('Facility reservation checked out', [
                'reservation_id' => $reservation->id,
                'checked_out_by' => $security->id,
            ]);

            return $reservation->fresh();
        });
    }

    private function checkForConflicts($facilityId, $startDate, $endDate, $startTime, $endTime, $excludeReservationId = null): void
    {
        $conflicts = FacilityReservation::where('facility_id', $facilityId)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate, $startTime, $endTime) {
                $query->where(function ($q) use ($startDate, $endDate, $startTime, $endTime) {
                    // Check if the new reservation overlaps with existing ones
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($subQ) use ($startDate, $endDate) {
                            $subQ->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                });
            })
            ->when($excludeReservationId, function ($query) use ($excludeReservationId) {
                $query->where('id', '!=', $excludeReservationId);
            })
            ->exists();

        if ($conflicts) {
            throw new \Exception('This facility is already reserved for the selected date and time.');
        }

        // Also check facility schedule
        $this->checkFacilitySchedule($facilityId, $startDate, $startTime, $endTime);
    }

    private function checkFacilitySchedule($facilityId, $date, $startTime, $endTime): void
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        
        $schedule = FacilitySchedule::where('facility_id', $facilityId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        // If no schedule exists, allow the reservation
        if (!$schedule) {
            return;
        }

        // Check if the requested time is within the available schedule
        $requestedStart = Carbon::parse($startTime);
        $requestedEnd = Carbon::parse($endTime);
        $scheduleStart = Carbon::parse($schedule->open_time);
        $scheduleEnd = Carbon::parse($schedule->close_time);

        if ($requestedStart < $scheduleStart || $requestedEnd > $scheduleEnd) {
            throw new \Exception('The requested time is outside the facility\'s operating hours.');
        }
    }

    private function hasDateTimeChanged(FacilityReservation $reservation, array $data): bool
    {
        return isset($data['start_date']) || isset($data['end_date']) ||
               isset($data['start_time']) || isset($data['end_time']) ||
               isset($data['facility_id']);
    }

    private function generateQRCode(FacilityReservation $reservation): string
    {
        $data = json_encode([
            'reservation_id' => $reservation->id,
            'facility_id' => $reservation->facility_id,
            'user_id' => $reservation->user_id,
            'start_date' => $reservation->start_date,
            'end_date' => $reservation->end_date,
        ]);

        $fileName = 'qr-codes/reservations/' . $reservation->id . '.png';
        $path = public_path($fileName);
        
        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        QrCode::format('png')
            ->size(300)
            ->margin(10)
            ->generate($data, $path);

        return $fileName;
    }

    public function getAvailableFacilities($startDate, $endDate, $startTime, $endTime)
    {
        $allFacilities = Facility::where('is_active', true)->get();
        
        $reservedFacilityIds = FacilityReservation::where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->pluck('facility_id')
            ->unique();

        return $allFacilities->whereNotIn('id', $reservedFacilityIds);
    }

    public function getUserReservations(User $user, $status = null)
    {
        $query = FacilityReservation::with(['facility', 'organization'])
            ->where('user_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getAllReservations($filters = [])
    {
        $query = FacilityReservation::with(['facility', 'user', 'organization']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['facility_id'])) {
            $query->where('facility_id', $filters['facility_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('start_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('end_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }
}
