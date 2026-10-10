<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ActivityLogService
{
    public function logActivity(array $data, User $user): ActivityLog
    {
        return DB::transaction(function () use ($data, $user) {
            $activityLog = ActivityLog::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'action' => $data['action'], // 'login', 'logout', 'create', 'update', 'delete', etc.
                'model_type' => $data['model_type'] ?? null,
                'model_id' => $data['model_id'] ?? null,
                'description' => $data['description'],
                'ip_address' => $data['ip_address'] ?? request()->ip(),
                'user_agent' => $data['user_agent'] ?? request()->userAgent(),
                'metadata' => $data['metadata'] ?? null,
            ]);

            Log::info('Activity logged', [
                'activity_log_id' => $activityLog->id,
                'user_id' => $user->id,
                'action' => $data['action'],
            ]);

            return $activityLog;
        });
    }

    public function getActivityLogs($filters = [])
    {
        $query = ActivityLog::with(['user']);

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['model_type'])) {
            $query->where('model_type', $filters['model_type']);
        }

        if (isset($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('description', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('user', function ($userQuery) use ($filters) {
                        $userQuery->where('name', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(50);
    }

    public function getUserActivities(User $user, $filters = [])
    {
        $filters['user_id'] = $user->id;
        return $this->getActivityLogs($filters);
    }

    public function getActivityStatistics(): array
    {
        return [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => ActivityLog::whereMonth('created_at', now()->month)->count(),
            'login' => ActivityLog::where('action', 'login')->count(),
            'logout' => ActivityLog::where('action', 'logout')->count(),
            'create' => ActivityLog::where('action', 'create')->count(),
            'update' => ActivityLog::where('action', 'update')->count(),
            'delete' => ActivityLog::where('action', 'delete')->count(),
        ];
    }

    public function cleanupOldLogs(int $daysToKeep = 90): int
    {
        return DB::transaction(function () use ($daysToKeep) {
            $count = ActivityLog::where('created_at', '<', now()->subDays($daysToKeep))->delete();

            Log::info('Old activity logs cleaned up', [
                'count' => $count,
                'days_kept' => $daysToKeep,
            ]);

            return $count;
        });
    }
}
