<?php

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuditTrailService
{
    public function recordChange(array $data, User $user): AuditTrail
    {
        return DB::transaction(function () use ($data, $user) {
            $auditTrail = AuditTrail::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'model_type' => $data['model_type'],
                'model_id' => $data['model_id'],
                'action' => $data['action'], // 'created', 'updated', 'deleted'
                'old_values' => $data['old_values'] ?? null,
                'new_values' => $data['new_values'] ?? null,
                'changed_fields' => $data['changed_fields'] ?? null,
                'ip_address' => $data['ip_address'] ?? request()->ip(),
                'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            ]);

            Log::info('Audit trail recorded', [
                'audit_trail_id' => $auditTrail->id,
                'user_id' => $user->id,
                'model_type' => $data['model_type'],
                'action' => $data['action'],
            ]);

            return $auditTrail;
        });
    }

    public function getAuditTrails($filters = [])
    {
        $query = AuditTrail::with(['user']);

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['model_type'])) {
            $query->where('model_type', $filters['model_type']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('model_type', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('user', function ($userQuery) use ($filters) {
                        $userQuery->where('name', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(50);
    }

    public function getModelAuditTrails(string $modelType, string $modelId)
    {
        return AuditTrail::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAuditStatistics(): array
    {
        return [
            'total' => AuditTrail::count(),
            'today' => AuditTrail::whereDate('created_at', today())->count(),
            'this_week' => AuditTrail::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => AuditTrail::whereMonth('created_at', now()->month)->count(),
            'created' => AuditTrail::where('action', 'created')->count(),
            'updated' => AuditTrail::where('action', 'updated')->count(),
            'deleted' => AuditTrail::where('action', 'deleted')->count(),
        ];
    }

    public function cleanupOldTrails(int $daysToKeep = 365): int
    {
        return DB::transaction(function () use ($daysToKeep) {
            $count = AuditTrail::where('created_at', '<', now()->subDays($daysToKeep))->delete();

            Log::info('Old audit trails cleaned up', [
                'count' => $count,
                'days_kept' => $daysToKeep,
            ]);

            return $count;
        });
    }
}
