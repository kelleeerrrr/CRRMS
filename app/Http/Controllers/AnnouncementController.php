<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncement;
use App\Models\Announcement;
use App\Services\AnnouncementService;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    protected AnnouncementService $announcementService;

    public function __construct(AnnouncementService $announcementService)
    {
        $this->announcementService = $announcementService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['category', 'audience', 'is_pinned', 'search']);
        $announcements = $this->announcementService->getAnnouncements($filters);
        $statistics = $this->announcementService->getAnnouncementStatistics();

        return view('announcements.index', compact('announcements', 'statistics'));
    }

    public function create()
    {
        // Only admins and school officers can create announcements
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('school_officer')) {
            abort(403, 'You do not have permission to create announcements.');
        }
        
        return view('announcements.create');
    }

    public function store(StoreAnnouncement $request)
    {
        // Only admins and school officers can create announcements
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('school_officer')) {
            abort(403, 'You do not have permission to create announcements.');
        }
        
        try {
            $announcement = $this->announcementService->createAnnouncement(
                $request->validated(),
                auth()->user()
            );

            return redirect()
                ->route('announcements.show', $announcement)
                ->with('success', 'Announcement created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Announcement $announcement)
    {
        $announcement->load('createdBy');

        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        // Only admins and school officers can edit announcements
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('school_officer')) {
            abort(403, 'You do not have permission to edit announcements.');
        }
        
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        // Only admins and school officers can update announcements
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('school_officer')) {
            abort(403, 'You do not have permission to update announcements.');
        }
        
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'category' => ['sometimes', 'in:general,urgent,event,information'],
            'is_pinned' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:published_at'],
            'audience' => ['sometimes', 'in:all,students,faculty,staff,specific_departments,specific_organizations'],
        ]);

        try {
            $announcement = $this->announcementService->updateAnnouncement($announcement, $data);

            return redirect()
                ->route('announcements.show', $announcement)
                ->with('success', 'Announcement updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Announcement $announcement)
    {
        // Only admins and school officers can delete announcements
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('school_officer')) {
            abort(403, 'You do not have permission to delete announcements.');
        }
        
        try {
            $this->announcementService->deleteAnnouncement($announcement);

            return redirect()
                ->route('announcements.index')
                ->with('success', 'Announcement deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function pin(Announcement $announcement)
    {
        // Only admins and school officers can pin announcements
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('school_officer')) {
            abort(403, 'You do not have permission to pin announcements.');
        }
        
        try {
            $announcement = $this->announcementService->pinAnnouncement($announcement);

            return back()
                ->with('success', 'Announcement pinned successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function unpin(Announcement $announcement)
    {
        // Only admins and school officers can unpin announcements
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('school_officer')) {
            abort(403, 'You do not have permission to unpin announcements.');
        }
        
        try {
            $announcement = $this->announcementService->unpinAnnouncement($announcement);

            return back()
                ->with('success', 'Announcement unpinned successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
