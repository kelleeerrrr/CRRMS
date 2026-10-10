<?php

namespace App\Services;

use App\Events\MaintenanceRequestStatusChanged;
use App\Models\MaintenanceLog;
use App\Models\Equipment;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MaintenanceService
{
    public function createMaintenanceLog(array $data, User $user): MaintenanceLog
    {
        return DB::transaction(function () use ($data, $user) {
            $maintenanceLog = MaintenanceLog::create([
                'id' => Str::uuid(),
                'type' => $data['type'], // 'equipment' or 'facility'
                'maintainable_id' => $data['maintainable_id'],
                'maintainable_type' => $data['maintainable_type'],
                'title' => $data['title'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? 'medium',
                'status' => 'pending',
                'assigned_to' => $data['assigned_to'] ?? null,
                'scheduled_date' => $data['scheduled_date'] ?? null,
                'completed_at' => null,
                'created_by' => $user->id,
            ]);

            Log::info('Maintenance log created', [
                'maintenance_log_id' => $maintenanceLog->id,
                'type' => $data['type'],
                'created_by' => $user->id,
            ]);

            return $maintenanceLog;
        });
    }

    public function updateMaintenanceLog(MaintenanceLog $maintenanceLog, array $data): MaintenanceLog
    {
        return DB::transaction(function () use ($maintenanceLog, $data) {
            $maintenanceLog->update($data);

            // Broadcast status change if status was updated
            if (isset($data['status'])) {
                broadcast(new MaintenanceRequestStatusChanged($maintenanceLog->fresh()));
            }

            Log::info('Maintenance log updated', [
                'maintenance_log_id' => $maintenanceLog->id,
            ]);

            return $maintenanceLog->fresh();
        });
    }

    public function completeMaintenance(MaintenanceLog $maintenanceLog, array $data): MaintenanceLog
    {
        return DB::transaction(function () use ($maintenanceLog, $data) {
            $maintenanceLog->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completion_notes' => $data['completion_notes'] ?? null,
                'cost' => $data['cost'] ?? null,
            ]);

            // Broadcast status change
            broadcast(new MaintenanceRequestStatusChanged($maintenanceLog->fresh()));

            Log::info('Maintenance completed', [
                'maintenance_log_id' => $maintenanceLog->id,
            ]);

            return $maintenanceLog->fresh();
        });
    }

    public function cancelMaintenance(MaintenanceLog $maintenanceLog, string $reason): MaintenanceLog
    {
        return DB::transaction(function () use ($maintenanceLog, $reason) {
            $maintenanceLog->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
            ]);

            Log::info('Maintenance cancelled', [
                'maintenance_log_id' => $maintenanceLog->id,
                'reason' => $reason,
            ]);

            return $maintenanceLog->fresh();
        });
    }

    public function deleteMaintenanceLog(MaintenanceLog $maintenanceLog): bool
    {
        return DB::transaction(function () use ($maintenanceLog) {
            $maintenanceLog->delete();

            Log::info('Maintenance log deleted', [
                'maintenance_log_id' => $maintenanceLog->id,
            ]);

            return true;
        });
    }

    public function getMaintenanceLogs($filters = [])
    {
        $query = MaintenanceLog::with(['maintainable', 'assignedTo', 'createdBy']);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getMaintenanceStatistics(): array
    {
        return [
            'total' => MaintenanceLog::count(),
            'pending' => MaintenanceLog::where('status', 'pending')->count(),
            'in_progress' => MaintenanceLog::where('status', 'in_progress')->count(),
            'completed' => MaintenanceLog::where('status', 'completed')->count(),
            'cancelled' => MaintenanceLog::where('status', 'cancelled')->count(),
            'equipment' => MaintenanceLog::where('type', 'equipment')->count(),
            'facility' => MaintenanceLog::where('type', 'facility')->count(),
            'high_priority' => MaintenanceLog::where('priority', 'high')->count(),
        ];
    }

    public function assignTechnician(MaintenanceLog $maintenanceLog, User $technician): MaintenanceLog
    {
        return DB::transaction(function () use ($maintenanceLog, $technician) {
            $maintenanceLog->update([
                'assigned_to' => $technician->id,
                'status' => 'in_progress',
            ]);

            Log::info('Technician assigned to maintenance', [
                'maintenance_log_id' => $maintenanceLog->id,
                'technician_id' => $technician->id,
            ]);

            return $maintenanceLog->fresh();
        });
    }

    public function getUpcomingMaintenance(int $days = 7)
    {
        return MaintenanceLog::where('status', 'pending')
            ->where('scheduled_date', '<=', now()->addDays($days))
            ->where('scheduled_date', '>=', now())
            ->with(['maintainable', 'assignedTo'])
            ->orderBy('scheduled_date')
            ->get();
    }

    public function getOverdueMaintenance()
    {
        return MaintenanceLog::where('status', 'pending')
            ->where('scheduled_date', '<', now())
            ->with(['maintainable', 'assignedTo'])
            ->orderBy('scheduled_date')
            ->get();
    }
}
