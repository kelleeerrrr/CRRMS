<?php

namespace App\Services;

use App\Events\ServiceRequestStatusChanged;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceRequestService
{
    public function createServiceRequest(array $data, User $user): ServiceRequest
    {
        return DB::transaction(function () use ($data, $user) {
            $serviceRequest = ServiceRequest::create([
                'id' => Str::uuid(),
                'type' => $data['type'], // 'cleaning', 'setup', 'technical_support', 'internet', 'electrical'
                'title' => $data['title'],
                'description' => $data['description'],
                'location' => $data['location'] ?? null,
                'priority' => $data['priority'] ?? 'medium',
                'status' => 'pending',
                'requested_by' => $user->id,
                'assigned_to' => $data['assigned_to'] ?? null,
                'scheduled_date' => $data['scheduled_date'] ?? null,
                'completed_at' => null,
            ]);

            Log::info('Service request created', [
                'service_request_id' => $serviceRequest->id,
                'type' => $data['type'],
                'requested_by' => $user->id,
            ]);

            return $serviceRequest;
        });
    }

    public function updateServiceRequest(ServiceRequest $serviceRequest, array $data): ServiceRequest
    {
        return DB::transaction(function () use ($serviceRequest, $data) {
            $serviceRequest->update($data);

            // Broadcast status change if status was updated
            if (isset($data['status'])) {
                broadcast(new ServiceRequestStatusChanged($serviceRequest->fresh()));
            }

            Log::info('Service request updated', [
                'service_request_id' => $serviceRequest->id,
            ]);

            return $serviceRequest->fresh();
        });
    }

    public function completeServiceRequest(ServiceRequest $serviceRequest, array $data): ServiceRequest
    {
        return DB::transaction(function () use ($serviceRequest, $data) {
            $serviceRequest->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completion_notes' => $data['completion_notes'] ?? null,
            ]);

            // Broadcast status change
            broadcast(new ServiceRequestStatusChanged($serviceRequest->fresh()));

            Log::info('Service request completed', [
                'service_request_id' => $serviceRequest->id,
            ]);

            return $serviceRequest->fresh();
        });
    }

    public function cancelServiceRequest(ServiceRequest $serviceRequest, string $reason): ServiceRequest
    {
        return DB::transaction(function () use ($serviceRequest, $reason) {
            $serviceRequest->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
            ]);

            Log::info('Service request cancelled', [
                'service_request_id' => $serviceRequest->id,
                'reason' => $reason,
            ]);

            return $serviceRequest->fresh();
        });
    }

    public function deleteServiceRequest(ServiceRequest $serviceRequest): bool
    {
        return DB::transaction(function () use ($serviceRequest) {
            $serviceRequest->delete();

            Log::info('Service request deleted', [
                'service_request_id' => $serviceRequest->id,
            ]);

            return true;
        });
    }

    public function getServiceRequests($filters = [])
    {
        $query = ServiceRequest::with(['requestedBy', 'assignedTo']);

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

        if (isset($filters['requested_by'])) {
            $query->where('requested_by', $filters['requested_by']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('location', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function getServiceRequestStatistics(): array
    {
        return [
            'total' => ServiceRequest::count(),
            'pending' => ServiceRequest::where('status', 'pending')->count(),
            'in_progress' => ServiceRequest::where('status', 'in_progress')->count(),
            'completed' => ServiceRequest::where('status', 'completed')->count(),
            'cancelled' => ServiceRequest::where('status', 'cancelled')->count(),
            'cleaning' => ServiceRequest::where('type', 'cleaning')->count(),
            'setup' => ServiceRequest::where('type', 'setup')->count(),
            'technical_support' => ServiceRequest::where('type', 'technical_support')->count(),
            'internet' => ServiceRequest::where('type', 'internet')->count(),
            'electrical' => ServiceRequest::where('type', 'electrical')->count(),
            'high_priority' => ServiceRequest::where('priority', 'high')->count(),
        ];
    }

    public function assignStaff(ServiceRequest $serviceRequest, User $staff): ServiceRequest
    {
        return DB::transaction(function () use ($serviceRequest, $staff) {
            $serviceRequest->update([
                'assigned_to' => $staff->id,
                'status' => 'in_progress',
            ]);

            Log::info('Staff assigned to service request', [
                'service_request_id' => $serviceRequest->id,
                'staff_id' => $staff->id,
            ]);

            return $serviceRequest->fresh();
        });
    }

    public function getUpcomingServiceRequests(int $days = 7)
    {
        return ServiceRequest::where('status', 'pending')
            ->where('scheduled_date', '<=', now()->addDays($days))
            ->where('scheduled_date', '>=', now())
            ->with(['requestedBy', 'assignedTo'])
            ->orderBy('scheduled_date')
            ->get();
    }

    public function getOverdueServiceRequests()
    {
        return ServiceRequest::where('status', 'pending')
            ->where('scheduled_date', '<', now())
            ->with(['requestedBy', 'assignedTo'])
            ->orderBy('scheduled_date')
            ->get();
    }
}
