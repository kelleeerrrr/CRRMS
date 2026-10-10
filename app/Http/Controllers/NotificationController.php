<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotification;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['is_read', 'type']);
        $notifications = $this->notificationService->getUserNotifications(Auth::user(), $filters);
        $unreadCount = $this->notificationService->getUnreadCount(Auth::user());

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function create()
    {
        $users = \App\Models\User::all();

        return view('notifications.create', compact('users'));
    }

    public function store(StoreNotification $request)
    {
        try {
            $notification = $this->notificationService->createNotification(
                $request->validated(),
                Auth::user()
            );

            return redirect()
                ->route('notifications.index')
                ->with('success', 'Notification created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Notification $notification)
    {
        $this->authorize('view', $notification);

        return view('notifications.show', compact('notification'));
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorize('update', $notification);

        try {
            $this->notificationService->markAsRead($notification);

            return back()
                ->with('success', 'Notification marked as read.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function markAllAsRead()
    {
        try {
            $count = $this->notificationService->markAllAsRead(Auth::user());

            return back()
                ->with('success', "{$count} notifications marked as read.");
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);

        try {
            $this->notificationService->deleteNotification($notification);

            return redirect()
                ->route('notifications.index')
                ->with('success', 'Notification deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function bulkSend(Request $request)
    {
        $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'type' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'data' => ['nullable', 'array'],
        ]);

        try {
            $count = $this->notificationService->sendBulkNotification(
                $request->user_ids,
                [
                    'type' => $request->type,
                    'title' => $request->title,
                    'message' => $request->message,
                    'data' => $request->data ?? null,
                ],
                Auth::user()
            );

            return back()
                ->with('success', "Notification sent to {$count} users.");
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function cleanup()
    {
        try {
            $count = $this->notificationService->cleanupOldNotifications(Auth::user());

            return back()
                ->with('success', "Cleaned up {$count} old notifications.");
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
