<?php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotificationService
{
    public function createNotification(array $data, User $user): Notification
    {
        return DB::transaction(function () use ($data, $user) {
            $notification = Notification::create([
                'id' => Str::uuid(),
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'title' => $data['title'],
                'message' => $data['message'],
                'data' => $data['data'] ?? null,
                'is_read' => false,
                'read_at' => null,
                'sent_via_email' => $data['sent_via_email'] ?? false,
                'sent_via_push' => $data['sent_via_push'] ?? false,
            ]);

            // Broadcast notification creation
            broadcast(new NotificationCreated($notification));

            Log::info('Notification created', [
                'notification_id' => $notification->id,
                'user_id' => $data['user_id'],
                'type' => $data['type'],
            ]);

            return $notification;
        });
    }

    public function markAsRead(Notification $notification): Notification
    {
        return DB::transaction(function () use ($notification) {
            if (!$notification->is_read) {
                $notification->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

                Log::info('Notification marked as read', [
                    'notification_id' => $notification->id,
                ]);
            }

            return $notification->fresh();
        });
    }

    public function markAllAsRead(User $user): int
    {
        return DB::transaction(function () use ($user) {
            $count = $user->notifications()->where('is_read', false)->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

            Log::info('All notifications marked as read', [
                'user_id' => $user->id,
                'count' => $count,
            ]);

            return $count;
        });
    }

    public function deleteNotification(Notification $notification): bool
    {
        return DB::transaction(function () use ($notification) {
            $notification->delete();

            Log::info('Notification deleted', [
                'notification_id' => $notification->id,
            ]);

            return true;
        });
    }

    public function getUserNotifications(User $user, $filters = [])
    {
        $query = $user->notifications();

        if (isset($filters['is_read'])) {
            $query->where('is_read', $filters['is_read']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getUnreadNotifications(User $user)
    {
        return $user->notifications()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUnreadCount(User $user): int
    {
        return $user->notifications()->where('is_read', false)->count();
    }

    public function sendBulkNotification(array $userIds, array $notificationData, User $sender): int
    {
        return DB::transaction(function () use ($userIds, $notificationData, $sender) {
            $count = 0;
            
            foreach ($userIds as $userId) {
                $this->createNotification([
                    'user_id' => $userId,
                    'type' => $notificationData['type'],
                    'title' => $notificationData['title'],
                    'message' => $notificationData['message'],
                    'data' => $notificationData['data'] ?? null,
                ], $sender);
                
                $count++;
            }

            Log::info('Bulk notification sent', [
                'sender_id' => $sender->id,
                'recipient_count' => $count,
            ]);

            return $count;
        });
    }

    public function cleanupOldNotifications(User $user, int $daysToKeep = 30): int
    {
        return DB::transaction(function () use ($user, $daysToKeep) {
            $count = $user->notifications()
                ->where('created_at', '<', now()->subDays($daysToKeep))
                ->where('is_read', true)
                ->delete();

            Log::info('Old notifications cleaned up', [
                'user_id' => $user->id,
                'count' => $count,
                'days_kept' => $daysToKeep,
            ]);

            return $count;
        });
    }
}
