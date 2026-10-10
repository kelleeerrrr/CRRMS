<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnnouncementService
{
    public function createAnnouncement(array $data, User $user): Announcement
    {
        return DB::transaction(function () use ($data, $user) {
            $announcement = Announcement::create([
                'id' => Str::uuid(),
                'title' => $data['title'],
                'content' => $data['content'],
                'category' => $data['category'] ?? 'general',
                'is_pinned' => $data['is_pinned'] ?? false,
                'published_at' => $data['published_at'] ?? now(),
                'expires_at' => $data['expires_at'] ?? null,
                'audience' => $data['audience'] ?? 'all',
                'created_by' => $user->id,
            ]);

            // Handle attachments if any
            if (isset($data['attachments']) && is_array($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    $announcement->attachments()->create([
                        'id' => Str::uuid(),
                        'file_name' => $attachment['file_name'],
                        'file_path' => $attachment['file_path'],
                        'file_size' => $attachment['file_size'] ?? null,
                        'file_type' => $attachment['file_type'] ?? null,
                    ]);
                }
            }

            Log::info('Announcement created', [
                'announcement_id' => $announcement->id,
                'title' => $data['title'],
                'created_by' => $user->id,
            ]);

            return $announcement;
        });
    }

    public function updateAnnouncement(Announcement $announcement, array $data): Announcement
    {
        return DB::transaction(function () use ($announcement, $data) {
            $announcement->update($data);

            Log::info('Announcement updated', [
                'announcement_id' => $announcement->id,
            ]);

            return $announcement->fresh();
        });
    }

    public function deleteAnnouncement(Announcement $announcement): bool
    {
        return DB::transaction(function () use ($announcement) {
            $announcement->attachments()->delete();
            $announcement->delete();

            Log::info('Announcement deleted', [
                'announcement_id' => $announcement->id,
            ]);

            return true;
        });
    }

    public function pinAnnouncement(Announcement $announcement): Announcement
    {
        return DB::transaction(function () use ($announcement) {
            // Unpin other announcements in the same category if needed
            Announcement::where('category', $announcement->category)
                ->where('id', '!=', $announcement->id)
                ->update(['is_pinned' => false]);

            $announcement->update(['is_pinned' => true]);

            Log::info('Announcement pinned', [
                'announcement_id' => $announcement->id,
            ]);

            return $announcement->fresh();
        });
    }

    public function unpinAnnouncement(Announcement $announcement): Announcement
    {
        return DB::transaction(function () use ($announcement) {
            $announcement->update(['is_pinned' => false]);

            Log::info('Announcement unpinned', [
                'announcement_id' => $announcement->id,
            ]);

            return $announcement->fresh();
        });

        return $announcement->fresh();
    }

    public function getAnnouncements($filters = [])
    {
        $query = Announcement::with(['createdBy', 'attachments']);

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['audience'])) {
            $query->where('audience', $filters['audience']);
        }

        if (isset($filters['is_pinned'])) {
            $query->where('is_pinned', $filters['is_pinned']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('content', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Only show published announcements
        $query->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });

        return $query->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(20);
    }

    public function getAnnouncementStatistics(): array
    {
        return [
            'total' => Announcement::count(),
            'pinned' => Announcement::where('is_pinned', true)->count(),
            'active' => Announcement::where('published_at', '<=', now())
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })->count(),
            'expired' => Announcement::where('expires_at', '<', now())->count(),
        ];
    }
}
