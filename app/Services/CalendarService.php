<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\EquipmentBorrowRequest;
use App\Models\FacilityReservation;
use App\Models\Resource;
use App\Models\ResourceBorrowRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CalendarService
{
    public function createEvent(array $data, User $user): CalendarEvent
    {
        return DB::transaction(function () use ($data, $user) {
            $event = CalendarEvent::create([
                'id' => Str::uuid(),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? $data['start_date'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'location' => $data['location'] ?? null,
                'event_type' => $data['event_type'] ?? 'general', // 'reservation', 'borrow', 'maintenance', 'event', 'holiday'
                'related_model_type' => $data['related_model_type'] ?? null,
                'related_model_id' => $data['related_model_id'] ?? null,
                'is_all_day' => $data['is_all_day'] ?? false,
                'is_recurring' => $data['is_recurring'] ?? false,
                'recurrence_pattern' => $data['recurrence_pattern'] ?? null,
                'created_by' => $user->id,
            ]);

            Log::info('Calendar event created', [
                'event_id' => $event->id,
                'title' => $data['title'],
                'created_by' => $user->id,
            ]);

            return $event;
        });
    }

    public function updateEvent(CalendarEvent $event, array $data): CalendarEvent
    {
        return DB::transaction(function () use ($event, $data) {
            $event->update($data);

            Log::info('Calendar event updated', [
                'event_id' => $event->id,
            ]);

            return $event->fresh();
        });
    }

    public function deleteEvent(CalendarEvent $event): bool
    {
        return DB::transaction(function () use ($event) {
            $event->delete();

            Log::info('Calendar event deleted', [
                'event_id' => $event->id,
            ]);

            return true;
        });
    }

    public function getEvents($filters = [])
    {
        $query = CalendarEvent::with(['createdBy']);

        if (isset($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        if (isset($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        if (isset($filters['related_model_type'])) {
            $query->where('related_model_type', $filters['related_model_type']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('location', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('start_date', 'asc')->get();
    }

    public function getEventsForCalendar($startDate, $endDate)
    {
        return CalendarEvent::whereBetween('start_date', [$startDate, $endDate])
            ->with(['createdBy'])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function getUpcomingEvents(int $days = 30)
    {
        return CalendarEvent::where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays($days))
            ->with(['createdBy'])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function getCalendarStatistics(): array
    {
        return [
            'total' => CalendarEvent::count(),
            'this_month' => CalendarEvent::whereMonth('start_date', now()->month)->count(),
            'upcoming' => CalendarEvent::where('start_date', '>=', now())->count(),
            'reservations' => CalendarEvent::where('event_type', 'reservation')->count(),
            'borrows' => CalendarEvent::where('event_type', 'borrow')->count(),
            'maintenance' => CalendarEvent::where('event_type', 'maintenance')->count(),
            'events' => CalendarEvent::where('event_type', 'event')->count(),
            'holidays' => CalendarEvent::where('event_type', 'holiday')->count(),
        ];
    }

    /**
     * Get user-specific calendar events from various sources
     */
    public function getUserCalendarEvents(User $user, $startDate, $endDate, $filter = 'all')
    {
        $events = collect();

        // Facility Reservations (user's approved reservations)
        if ($filter === 'all' || $filter === 'reservations') {
            $facilityReservations = FacilityReservation::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'pending'])
                ->whereBetween('start_date', [$startDate, $endDate])
                ->with('facility')
                ->get()
                ->map(function ($reservation) {
                    return [
                        'id' => $reservation->id,
                        'title' => 'Facility: ' . $reservation->facility->name,
                        'start' => $reservation->start_date->format('Y-m-d') . ($reservation->start_time ? 'T' . $reservation->start_time->format('H:i:s') : ''),
                        'end' => $reservation->end_date->format('Y-m-d') . ($reservation->end_time ? 'T' . $reservation->end_time->format('H:i:s') : ''),
                        'backgroundColor' => $reservation->status === 'approved' ? '#10b981' : '#f59e0b',
                        'borderColor' => $reservation->status === 'approved' ? '#059669' : '#d97706',
                        'extendedProps' => [
                            'type' => 'facility_reservation',
                            'status' => $reservation->status,
                            'facility_name' => $reservation->facility->name,
                            'start_time' => $reservation->start_time ? $reservation->start_time->format('g:i A') : null,
                            'end_time' => $reservation->end_time ? $reservation->end_time->format('g:i A') : null,
                            'description' => $reservation->description,
                        ],
                    ];
                });
            $events = $events->concat($facilityReservations);
        }

        // Equipment Borrowing (user's approved equipment borrow requests)
        // Commented out - equipment_borrow_requests table doesn't have start_date column
        // if ($filter === 'all' || $filter === 'borrowing') {
        //     $equipmentBorrows = EquipmentBorrowRequest::where('user_id', $user->id)
        //         ->whereIn('status', ['approved', 'pending'])
        //         ->whereBetween('start_date', [$startDate, $endDate])
        //         ->with('items.equipment')
        //         ->get()
        //         ->map(function ($borrow) {
        //             $equipmentNames = $borrow->items->pluck('equipment.name')->implode(', ');
        //             return [
        //                 'id' => $borrow->id,
        //                 'title' => 'Equipment: ' . $equipmentNames,
        //                 'start' => $borrow->start_date->format('Y-m-d'),
        //                 'end' => $borrow->end_date->format('Y-m-d'),
        //                 'backgroundColor' => $borrow->status === 'approved' ? '#8b5cf6' : '#f59e0b',
        //                 'borderColor' => $borrow->status === 'approved' ? '#7c3aed' : '#d97706',
        //                 'extendedProps' => [
        //                     'type' => 'equipment_borrow',
        //                     'status' => $borrow->status,
        //                     'equipment_names' => $equipmentNames,
        //                     'purpose' => $borrow->purpose,
        //                 ],
        //             ];
        //         });
        //     $events = $events->concat($equipmentBorrows);
        // }

        // Resource Borrowing (resources borrowed by the user)
        if ($filter === 'all' || $filter === 'borrowing') {
            $resourceBorrows = ResourceBorrowRequest::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'pending'])
                ->whereBetween('start_date', [$startDate, $endDate])
                ->with('resource')
                ->get()
                ->map(function ($borrow) {
                    return [
                        'id' => $borrow->id,
                        'title' => 'Borrowed: ' . $borrow->resource->name,
                        'start' => $borrow->start_date->format('Y-m-d'),
                        'end' => $borrow->end_date->format('Y-m-d'),
                        'backgroundColor' => $borrow->status === 'approved' ? '#3b82f6' : '#f59e0b',
                        'borderColor' => $borrow->status === 'approved' ? '#2563eb' : '#d97706',
                        'extendedProps' => [
                            'type' => 'resource_borrow',
                            'status' => $borrow->status,
                            'resource_name' => $borrow->resource->name,
                            'resource_condition' => $borrow->resource->condition,
                            'purpose' => $borrow->purpose,
                        ],
                    ];
                });
            $events = $events->concat($resourceBorrows);
        }

        // Resource Lending (user's resources borrowed by others)
        if ($filter === 'all' || $filter === 'lending') {
            $userResourceIds = Resource::where('owner_type', 'user')
                ->where('owner_id', $user->id)
                ->pluck('id');

            $resourceLending = ResourceBorrowRequest::whereIn('resource_id', $userResourceIds)
                ->whereIn('status', ['approved', 'pending'])
                ->whereBetween('start_date', [$startDate, $endDate])
                ->with(['resource', 'user'])
                ->get()
                ->map(function ($borrow) {
                    return [
                        'id' => $borrow->id,
                        'title' => 'Lent to: ' . $borrow->user->name,
                        'start' => $borrow->start_date->format('Y-m-d'),
                        'end' => $borrow->end_date->format('Y-m-d'),
                        'backgroundColor' => $borrow->status === 'approved' ? '#ec4899' : '#f59e0b',
                        'borderColor' => $borrow->status === 'approved' ? '#db2777' : '#d97706',
                        'extendedProps' => [
                            'type' => 'resource_lending',
                            'status' => $borrow->status,
                            'resource_name' => $borrow->resource->name,
                            'borrower_name' => $borrow->user->name,
                            'purpose' => $borrow->purpose,
                        ],
                    ];
                });
            $events = $events->concat($resourceLending);
        }

        return $events;
    }
}
