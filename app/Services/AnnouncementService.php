<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnnouncementService
{
    public function createAnnouncement(array $data, User $user): Announcement
    {
        return DB::transaction(function () use ($data, $user) {
            $photoPath = null;
            if (isset($data['photo'])) {
                $photoPath = $data['photo']->store('announcements', 'public');
            }

            $announcement = Announcement::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'category' => $data['category'] ?? 'general',
                'is_pinned' => $data['is_pinned'] ?? false,
                'published_at' => $data['published_at'] ?? now(),
                'expires_at' => $data['expires_at'] ?? null,
                'photo' => $photoPath,
                'attachments' => $data['attachments'] ?? null,
                'created_by' => $user->id,
            ]);

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
            // Handle photo upload
            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                // Delete old photo if exists
                if ($announcement->photo) {
                    \Storage::disk('public')->delete($announcement->photo);
                }
                $data['photo'] = $data['photo']->store('announcements', 'public');
            }

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
    }

    public function getAnnouncements($filters = [])
    {
        $query = Announcement::with('createdBy');

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
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
        $publishedQuery = Announcement::where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });

        return [
            'total' => $publishedQuery->count(),
            'pinned' => $publishedQuery->where('is_pinned', true)->count(),
            'active' => $publishedQuery->count(),
            'expired' => Announcement::where('published_at', '<=', now())
                ->where('expires_at', '<', now())->count(),
        ];
    }
}
