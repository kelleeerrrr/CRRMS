<?php

namespace App\Services;

use App\Events\ResourceBorrowRequestStatusChanged;
use App\Models\Resource;
use App\Models\ResourceBorrowRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResourceBorrowRequestService
{
    public function createBorrowRequest(array $data, User $user): ResourceBorrowRequest
    {
        return DB::transaction(function () use ($data, $user) {
            $resource = Resource::findOrFail($data['resource_id']);
            
            // Check if resource is available for borrowing
            if (!$resource->available_for_borrowing) {
                throw new \Exception("Resource {$resource->name} is not available for borrowing.");
            }

            if ($resource->status !== 'available') {
                throw new \Exception("Resource {$resource->name} is currently unavailable.");
            }

            if ($resource->owner_id === $user->id) {
                throw new \Exception("You cannot borrow your own resource.");
            }

            if ($data['quantity'] > $resource->available_quantity) {
                throw new \Exception("Requested quantity exceeds available quantity. Available: {$resource->available_quantity}, Requested: {$data['quantity']}");
            }

            // Check for overlapping requests
            $this->checkForOverlappingRequests($resource, $data['start_date'], $data['end_date'], $user->id);

            $request = ResourceBorrowRequest::create([
                'id' => Str::uuid(),
                'resource_id' => $data['resource_id'],
                'user_id' => $user->id,
                'borrower_name' => $data['borrower_name'],
                'contact_number' => $data['contact_number'],
                'email' => $data['email'],
                'quantity' => $data['quantity'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'purpose' => $data['purpose'],
                'intended_use' => $data['intended_use'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);

            // Send notification to admins/school officers (skip if fails)
            try {
                $admins = User::whereHas('roles', function($query) {
                    $query->where('name', 'super_admin')
                        ->orWhere('name', 'admin')
                        ->orWhere('name', 'school_officer')
                        ->orWhere('name', 'property_custodian');
                })->get();
                
                foreach ($admins as $admin) {
                    app(\App\Services\NotificationService::class)->createNotification([
                        'user_id' => $admin->id,
                        'type' => 'resource_borrow_request_created',
                        'title' => 'New Resource Borrow Request',
                        'message' => "A new resource borrow request has been submitted by {$user->name}.",
                        'data' => [
                            'request_id' => $request->id,
                            'resource_id' => $request->resource_id,
                            'user_id' => $user->id,
                            'resource_name' => $request->resource->name,
                            'quantity' => $request->quantity,
                        ],
                    ], $user);
                }
            } catch (\Exception $e) {
                Log::error('Notification sending failed', ['error' => $e->getMessage()]);
            }

            Log::info('Resource borrow request created', [
                'request_id' => $request->id,
                'user_id' => $user->id,
                'resource_id' => $data['resource_id'],
                'quantity' => $data['quantity'],
            ]);

            return $request->load(['resource', 'user']);
        });
    }

    public function approveBorrowRequest(ResourceBorrowRequest $request, User $approver): ResourceBorrowRequest
    {
        return DB::transaction(function () use ($request, $approver) {
            // Lock the resource to prevent concurrent approvals
            $resource = Resource::where('id', $request->resource_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Recheck availability before approving
            if (!$resource->available_for_borrowing) {
                throw new \Exception("Resource {$resource->name} is no longer available for borrowing.");
            }

            if ($resource->status !== 'available') {
                throw new \Exception("Resource {$resource->name} is currently unavailable.");
            }

            if ($request->quantity > $resource->available_quantity) {
                throw new \Exception("Insufficient quantity for {$resource->name}. Available: {$resource->available_quantity}, Requested: {$request->quantity}");
            }

            // Verify authorization
            if (!$request->canBeApprovedBy($approver)) {
                throw new \Exception("You are not authorized to approve this request.");
            }

            // Update resource available quantity
            $resource->decrement('available_quantity', $request->quantity);

            // Approve the request
            $request->update([
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => now(),
            ]);

            // Broadcast status change
            try {
                broadcast(new ResourceBorrowRequestStatusChanged($request->fresh()));
            } catch (\Exception $e) {
                Log::error('Broadcast failed', ['error' => $e->getMessage()]);
            }

            // Create notification
            try {
                app(\App\Services\NotificationService::class)->createNotification([
                    'user_id' => $request->user_id,
                    'type' => 'resource_borrow_request_approved',
                    'title' => 'Resource Borrow Request Approved',
                    'message' => "Your resource borrow request for {$resource->name} has been approved.",
                    'data' => [
                        'request_id' => $request->id,
                        'resource_name' => $resource->name,
                        'quantity' => $request->quantity,
                    ],
                ], $approver);
            } catch (\Exception $e) {
                Log::error('Notification failed', ['error' => $e->getMessage()]);
            }

            Log::info('Resource borrow request approved', [
                'request_id' => $request->id,
                'approved_by' => $approver->id,
            ]);

            return $request->fresh();
        });
    }

    public function rejectBorrowRequest(ResourceBorrowRequest $request, User $approver, ?string $reason = null): ResourceBorrowRequest
    {
        return DB::transaction(function () use ($request, $approver, $reason) {
            // Verify authorization
            if (!$request->canBeApprovedBy($approver)) {
                throw new \Exception("You are not authorized to reject this request.");
            }

            $request->update([
                'status' => 'rejected',
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'rejection_reason' => $reason,
            ]);

            // Broadcast status change
            try {
                broadcast(new ResourceBorrowRequestStatusChanged($request->fresh()));
            } catch (\Exception $e) {
                Log::error('Broadcast failed', ['error' => $e->getMessage()]);
            }

            // Create notification
            try {
                app(\App\Services\NotificationService::class)->createNotification([
                    'user_id' => $request->user_id,
                    'type' => 'resource_borrow_request_rejected',
                    'title' => 'Resource Borrow Request Rejected',
                    'message' => "Your resource borrow request has been rejected.",
                    'data' => [
                        'request_id' => $request->id,
                        'rejection_reason' => $reason,
                    ],
                ], $approver);
            } catch (\Exception $e) {
                Log::error('Notification failed', ['error' => $e->getMessage()]);
            }

            Log::info('Resource borrow request rejected', [
                'request_id' => $request->id,
                'approved_by' => $approver->id,
                'reason' => $reason,
            ]);

            return $request->fresh();
        });
    }

    public function cancelBorrowRequest(ResourceBorrowRequest $request, User $user): ResourceBorrowRequest
    {
        return DB::transaction(function () use ($request, $user) {
            if ($request->user_id !== $user->id) {
                throw new \Exception('You can only cancel your own requests.');
            }

            if ($request->status === 'approved') {
                // Restore quantity if cancelling an approved request
                $resource = Resource::where('id', $request->resource_id)
                    ->lockForUpdate()
                    ->firstOrFail();
                
                $resource->increment('available_quantity', $request->quantity);
            }

            if ($request->status === 'approved' || $request->status === 'in_use') {
                throw new \Exception('Cannot cancel a request that has been approved or is in use.');
            }

            $request->update([
                'status' => 'cancelled',
                'cancelled_by' => $user->id,
                'cancelled_at' => now(),
            ]);

            // Broadcast status change
            try {
                broadcast(new ResourceBorrowRequestStatusChanged($request->fresh()));
            } catch (\Exception $e) {
                Log::error('Broadcast failed', ['error' => $e->getMessage()]);
            }

            Log::info('Resource borrow request cancelled', [
                'request_id' => $request->id,
                'cancelled_by' => $user->id,
            ]);

            return $request->fresh();
        });
    }

    public function updateBorrowRequest(ResourceBorrowRequest $request, array $data, User $user): ResourceBorrowRequest
    {
        return DB::transaction(function () use ($request, $data, $user) {
            if ($request->user_id !== $user->id) {
                throw new \Exception('You can only update your own requests.');
            }

            if ($request->status !== 'pending') {
                throw new \Exception('Cannot update a request that is not pending.');
            }

            $resource = Resource::findOrFail($request->resource_id);

            if (isset($data['quantity']) && $data['quantity'] > $resource->available_quantity) {
                throw new \Exception("Requested quantity exceeds available quantity. Available: {$resource->available_quantity}, Requested: {$data['quantity']}");
            }

            // Check for overlapping requests if dates changed
            if (isset($data['start_date']) || isset($data['end_date'])) {
                $startDate = $data['start_date'] ?? $request->start_date;
                $endDate = $data['end_date'] ?? $request->end_date;
                $this->checkForOverlappingRequests($resource, $startDate, $endDate, $user->id, $request->id);
            }

            $request->update($data);

            Log::info('Resource borrow request updated', [
                'request_id' => $request->id,
                'updated_by' => $user->id,
            ]);

            return $request->fresh();
        });
    }

    public function checkOutResource(ResourceBorrowRequest $request, User $staff): ResourceBorrowRequest
    {
        return DB::transaction(function () use ($request, $staff) {
            if ($request->status !== 'approved') {
                throw new \Exception('Only approved requests can be checked out.');
            }

            $request->update([
                'status' => 'in_use',
                'checked_out_at' => now(),
                'checked_out_by' => $staff->id,
            ]);

            // Broadcast status change
            try {
                broadcast(new ResourceBorrowRequestStatusChanged($request->fresh()));
            } catch (\Exception $e) {
                Log::error('Broadcast failed', ['error' => $e->getMessage()]);
            }

            Log::info('Resource borrow request checked out', [
                'request_id' => $request->id,
                'checked_out_by' => $staff->id,
            ]);

            return $request->fresh();
        });
    }

    public function returnResource(ResourceBorrowRequest $request, User $staff): ResourceBorrowRequest
    {
        return DB::transaction(function () use ($request, $staff) {
            if ($request->status !== 'in_use') {
                throw new \Exception('Only in-use requests can be returned.');
            }

            // Restore resource quantity
            $resource = Resource::where('id', $request->resource_id)
                ->lockForUpdate()
                ->firstOrFail();
            
            $resource->increment('available_quantity', $request->quantity);

            $request->update([
                'status' => 'returned',
                'actual_return_date' => now(),
                'returned_at' => now(),
                'returned_by' => $staff->id,
            ]);

            // Broadcast status change
            try {
                broadcast(new ResourceBorrowRequestStatusChanged($request->fresh()));
            } catch (\Exception $e) {
                Log::error('Broadcast failed', ['error' => $e->getMessage()]);
            }

            Log::info('Resource borrow request returned', [
                'request_id' => $request->id,
                'returned_by' => $staff->id,
            ]);

            return $request->fresh();
        });
    }

    public function getUserBorrowRequests(User $user, $status = null)
    {
        $query = ResourceBorrowRequest::with(['resource.owner', 'user'])
            ->where('user_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(12);
    }

    public function getAllBorrowRequests($filters = [])
    {
        $query = ResourceBorrowRequest::with(['resource.owner', 'user']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->whereHas('resource', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%");
            })->orWhereHas('user', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(12);
    }

    public function getResourcePendingRequests(Resource $resource)
    {
        return ResourceBorrowRequest::with('user')
            ->where('resource_id', $resource->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function checkForOverlappingRequests(Resource $resource, $startDate, $endDate, $userId, $excludeRequestId = null)
    {
        $query = ResourceBorrowRequest::where('resource_id', $resource->id)
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved', 'in_use'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q) use ($startDate, $endDate) {
                      $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                  });
            });

        if ($excludeRequestId) {
            $query->where('id', '!=', $excludeRequestId);
        }

        $overlapping = $query->exists();

        if ($overlapping) {
            throw new \Exception('You already have a borrowing request for this resource that overlaps with the requested dates.');
        }
    }
}
