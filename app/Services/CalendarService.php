<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\EquipmentBorrowRequest;
use App\Models\FacilityReservation;
use App\Models\ResourceBorrowRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
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

    public const REQUEST_TYPES = [
        'reservations' => ['label' => 'Facilities', 'type' => 'Facility reservation', 'color' => '#1d4ed8'],
        'equipment' => ['label' => 'Equipment', 'type' => 'Equipment borrowing', 'color' => '#7e22ce'],
        'resources' => ['label' => 'Resources', 'type' => 'Resource borrowing', 'color' => '#047857'],
    ];

    public function getUserCalendarEvents(User $user, string $startDate, string $endDate, string $filter = 'all')
    {
        $isOfficer = $user->hasRole('school_officer');
        $events = collect();

        if (in_array($filter, ['all', 'reservations'], true)) {
            $query = $this->scheduledRequests(
                FacilityReservation::with(['facility', 'user']),
                $user, $isOfficer, ['approved', 'reserved', 'checked_in', 'completed'],
                'start_date', 'end_date', $startDate, $endDate
            );

            $events = $events->concat($query->get()->map(function ($reservation) {
                $start = $reservation->start_date;
                $end = $reservation->end_date;
                $timed = $reservation->start_time && $reservation->end_time;
                if ($timed) {
                    $start = $start->copy()->setTimeFrom($reservation->start_time);
                    $end = $end->copy()->setTimeFrom($reservation->end_time);
                }

                return $this->requestEvent(
                    'reservations', $reservation->id, $reservation->status, $start, $end,
                    $reservation->facility?->name ?? 'Facility',
                    $reservation->reservee_name ?: ($reservation->user?->name ?? 'Requester'),
                    route('facilities.reservations.show', $reservation),
                    $reservation->description, !$timed
                );
            }));
        }

        if (in_array($filter, ['all', 'borrowing', 'equipment'], true)) {
            $query = $this->scheduledRequests(
                EquipmentBorrowRequest::with(['items.equipment', 'user']),
                $user, $isOfficer, ['approved', 'in_use', 'returned', 'completed'],
                'borrow_date', 'return_date', $startDate, $endDate
            );

            $events = $events->concat($query->get()->map(function ($borrow) {
                $equipment = $borrow->items->map(fn ($item) => ($item->equipment?->name ?? 'Equipment')
                    . ($item->quantity > 1 ? ' ×' . $item->quantity : ''))->implode(', ');

                return $this->requestEvent(
                    'equipment', $borrow->id, $borrow->status, $borrow->borrow_date, $borrow->return_date,
                    $equipment ?: 'Equipment', $borrow->user?->name ?? 'Requester',
                    route('equipment.borrow-requests.show', $borrow), $borrow->purpose
                );
            }));
        }

        if (in_array($filter, ['all', 'borrowing', 'lending', 'resources'], true)) {
            $ownedResources = fn (Builder $query) => $query->where('owner_type', 'user')->where('owner_id', $user->id);
            $query = $this->scheduledRequests(
                ResourceBorrowRequest::with(['resource', 'user']),
                $user, $isOfficer, ['approved', 'in_use', 'returned'],
                'start_date', 'end_date', $startDate, $endDate, scopeToUser: false
            );

            if ($filter === 'lending') {
                $query->whereHas('resource', $ownedResources);
            } elseif (!$isOfficer) {
                $query->where(function (Builder $query) use ($user, $filter, $ownedResources) {
                    $query->where('user_id', $user->id);
                    if ($filter === 'all') {
                        $query->orWhereHas('resource', $ownedResources);
                    }
                });
            }

            $events = $events->concat($query->get()->map(fn ($borrow) => $this->requestEvent(
                'resources', $borrow->id, $borrow->status, $borrow->start_date, $borrow->end_date,
                $borrow->resource?->name ?? 'Resource',
                $borrow->borrower_name ?: ($borrow->user?->name ?? 'Requester'),
                route('resource-borrow-requests.show', $borrow), $borrow->purpose
            )));
        }

        return $events;
    }

    private function scheduledRequests(
        Builder $query, User $user, bool $isOfficer, array $statuses,
        string $startColumn, string $endColumn, string $startDate, string $endDate,
        bool $scopeToUser = true
    ): Builder {
        if (!$isOfficer) {
            $statuses[] = 'pending';
            if ($scopeToUser) {
                $query->where('user_id', $user->id);
            }
        }

        return $query->whereIn('status', $statuses)
            ->where($startColumn, '<', $endDate)
            ->where($endColumn, '>=', $startDate);
    }

    private function requestEvent(
        string $category, string $id, string $status, Carbon $start, Carbon $end,
        string $item, string $requester, string $detailsUrl, ?string $description, bool $allDay = true
    ): array {
        $type = self::REQUEST_TYPES[$category];

        return [
            'id' => "$category:$id",
            'title' => "$requester · $item",
            'start' => $allDay ? $start->toDateString() : $start->toDateTimeLocalString(),
            'end' => $allDay ? $end->copy()->addDay()->toDateString() : $end->toDateTimeLocalString(),
            'allDay' => $allDay,
            'color' => $status === 'pending' ? '#92400e' : $type['color'],
            'extendedProps' => [
                'type' => $type['type'],
                'item' => $item,
                'requester' => $requester,
                'status' => $status,
                'period' => $start->format($allDay ? 'M j, Y' : 'M j, Y g:i A')
                    . ' – ' . $end->format($allDay ? 'M j, Y' : 'M j, Y g:i A'),
                'description' => $description,
                'detailsUrl' => $detailsUrl,
            ],
        ];
    }
}
