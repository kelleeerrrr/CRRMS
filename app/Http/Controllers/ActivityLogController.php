<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityLog;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', ActivityLog::class);

        $filters = $request->only(['user_id', 'action', 'model_type', 'start_date', 'end_date', 'search']);
        $activityLogs = $this->activityLogService->getActivityLogs($filters);
        $statistics = $this->activityLogService->getActivityStatistics();
        $users = User::all();

        return view('activity-logs.index', compact('activityLogs', 'statistics', 'users'));
    }

    public function show(ActivityLog $activityLog)
    {
        $this->authorize('view', $activityLog);

        $activityLog->load('user');

        return view('activity-logs.show', compact('activityLog'));
    }

    public function store(StoreActivityLog $request)
    {
        try {
            $activityLog = $this->activityLogService->logActivity(
                $request->validated(),
                auth()->user()
            );

            return back()
                ->with('success', 'Activity logged successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function cleanup()
    {
        $this->authorize('delete', ActivityLog::class);

        try {
            $count = $this->activityLogService->cleanupOldLogs();

            return back()
                ->with('success', "Cleaned up {$count} old activity logs.");
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
