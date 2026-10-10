<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarEvent;
use App\Models\CalendarEvent;
use App\Models\User;
use App\Services\CalendarService;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    protected CalendarService $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $startDate = $request->get('start', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end', now()->endOfMonth()->format('Y-m-d'));

        $events = $this->calendarService->getUserCalendarEvents(auth()->user(), $startDate, $endDate, $filter);

        return view('calendar.index', compact('events', 'filter', 'startDate', 'endDate'));
    }

    public function create()
    {
        return view('calendar.create');
    }

    public function store(StoreCalendarEvent $request)
    {
        try {
            $event = $this->calendarService->createEvent(
                $request->validated(),
                auth()->user()
            );

            return redirect()
                ->route('calendar.show', $event)
                ->with('success', 'Calendar event created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(CalendarEvent $event)
    {
        $event->load('createdBy');

        return view('calendar.show', compact('event'));
    }

    public function edit(CalendarEvent $event)
    {
        $event->load('createdBy');

        return view('calendar.edit', compact('event'));
    }

    public function update(Request $request, CalendarEvent $event)
    {
        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable', 'after:start_time'],
            'location' => ['nullable', 'string'],
            'event_type' => ['sometimes', 'in:reservation,borrow,maintenance,event,holiday'],
            'is_all_day' => ['sometimes', 'boolean'],
            'is_recurring' => ['sometimes', 'boolean'],
            'recurrence_pattern' => ['nullable', 'in:daily,weekly,monthly,yearly'],
        ]);

        try {
            $event = $this->calendarService->updateEvent($event, $request->validated());

            return redirect()
                ->route('calendar.show', $event)
                ->with('success', 'Calendar event updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(CalendarEvent $event)
    {
        try {
            $this->calendarService->deleteEvent($event);

            return redirect()
                ->route('calendar.index')
                ->with('success', 'Calendar event deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function calendarData(Request $request)
    {
        $startDate = $request->get('start', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end', now()->endOfMonth()->format('Y-m-d'));
        $filter = $request->get('filter', 'all');

        $events = $this->calendarService->getUserCalendarEvents(auth()->user(), $startDate, $endDate, $filter);

        return response()->json($events);
    }
}
