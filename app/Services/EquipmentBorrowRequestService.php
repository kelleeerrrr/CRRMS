<?php

namespace App\Services;

use App\Events\EquipmentBorrowRequestStatusChanged;
use App\Models\Equipment;
use App\Models\EquipmentBorrowRequest;
use App\Models\EquipmentBorrowItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class EquipmentBorrowRequestService
{
    public function createBorrowRequest(array $data, User $user): EquipmentBorrowRequest
    {
        return DB::transaction(function () use ($data, $user) {
            $request = EquipmentBorrowRequest::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'purpose' => $data['purpose'],
                'borrow_date' => $data['borrow_date'],
                'return_date' => $data['return_date'],
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'qr_code' => null,
            ]);

            // Add the equipment item
            $equipment = Equipment::findOrFail($data['equipment_id']);
            
            // Check if equipment is available for borrowing
            if (!$equipment->isAvailableForBorrowing()) {
                throw new \Exception("Equipment {$equipment->name} is not available for borrowing.");
            }

            EquipmentBorrowItem::create([
                'id' => Str::uuid(),
                'borrow_request_id' => $request->id,
                'equipment_id' => $data['equipment_id'],
                'quantity' => $data['quantity'],
                'condition_at_borrow' => $equipment->condition,
            ]);

            // Generate QR code
            try {
                $request->qr_code = $this->generateQRCode($request);
                $request->save();
            } catch (\Exception $e) {
                Log::error('QR code generation failed', ['error' => $e->getMessage()]);
            }

            Log::info('Equipment borrow request created', [
                'request_id' => $request->id,
                'user_id' => $user->id,
                'equipment_id' => $data['equipment_id'],
                'quantity' => $data['quantity'],
            ]);

            return $request->load('items.equipment');
        });
    }

    public function approveBorrowRequest(EquipmentBorrowRequest $request, User $approver): EquipmentBorrowRequest
    {
        return DB::transaction(function () use ($request, $approver) {
            // Lock the equipment records to prevent concurrent approvals
            $equipmentIds = $request->items->pluck('equipment_id')->toArray();
            $lockedEquipment = Equipment::whereIn('id', $equipmentIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Recheck availability for each item before approving
            foreach ($request->items as $item) {
                $equipment = $lockedEquipment->get($item->equipment_id);
                
                if (!$equipment) {
                    throw new \Exception("Equipment not found.");
                }
                
                $availableQuantity = $equipment->getAvailableQuantityAttribute();
                
                if ($item->quantity > $availableQuantity) {
                    throw new \Exception("Insufficient quantity for {$equipment->name}. Available: {$availableQuantity}, Requested: {$item->quantity}");
                }
                
                // Check if equipment is still available for borrowing
                if (!$equipment->isAvailableForBorrowing()) {
                    throw new \Exception("Equipment {$equipment->name} is no longer available for borrowing.");
                }
            }

            // Approve the request
            $request->update([
                'status' => 'approved',
                'approved_by' => $approver->id,
                'approved_at' => now(),
            ]);

            // Broadcast status change
            try {
                broadcast(new EquipmentBorrowRequestStatusChanged($request->fresh()));
            } catch (\Exception $e) {
                Log::error('Broadcast failed', ['error' => $e->getMessage()]);
            }

            Log::info('Equipment borrow request approved', [
                'request_id' => $request->id,
                'approved_by' => $approver->id,
            ]);

            return $request->fresh();
        });
    }

    public function rejectBorrowRequest(EquipmentBorrowRequest $request, User $approver, string $reason): EquipmentBorrowRequest
    {
        return DB::transaction(function () use ($request, $approver, $reason) {
            $request->update([
                'status' => 'rejected',
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'rejection_reason' => $reason,
            ]);

            // No need to release equipment since pending requests don't reserve quantity

            // Broadcast status change
            try {
                broadcast(new EquipmentBorrowRequestStatusChanged($request->fresh()));
            } catch (\Exception $e) {
                Log::error('Broadcast failed', ['error' => $e->getMessage()]);
            }

            Log::info('Equipment borrow request rejected', [
                'request_id' => $request->id,
                'approved_by' => $approver->id,
                'reason' => $reason,
            ]);

            return $request->fresh();
        });
    }

    public function cancelBorrowRequest(EquipmentBorrowRequest $request, User $user): EquipmentBorrowRequest
    {
        return DB::transaction(function () use ($request, $user) {
            if ($request->status === 'approved' || $request->status === 'in_use') {
                throw new \Exception('Cannot cancel a request that has been approved or is in use.');
            }

            $request->update([
                'status' => 'cancelled',
                'cancelled_by' => $user->id,
                'cancelled_at' => now(),
            ]);

            // No need to release equipment since pending requests don't reserve quantity

            Log::info('Equipment borrow request cancelled', [
                'request_id' => $request->id,
                'cancelled_by' => $user->id,
            ]);

            return $request->fresh();
        });
    }

    public function checkOutEquipment(EquipmentBorrowRequest $request, User $staff): EquipmentBorrowRequest
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

            Log::info('Equipment borrow request checked out', [
                'request_id' => $request->id,
                'checked_out_by' => $staff->id,
            ]);

            return $request->fresh();
        });
    }

    public function returnEquipment(EquipmentBorrowRequest $request, array $returnData, User $staff): EquipmentBorrowRequest
    {
        return DB::transaction(function () use ($request, $returnData, $staff) {
            if ($request->status !== 'in_use') {
                throw new \Exception('Only in-use requests can be returned.');
            }

            foreach ($returnData['items'] as $itemData) {
                $item = EquipmentBorrowItem::where('borrow_request_id', $request->id)
                    ->where('equipment_id', $itemData['equipment_id'])
                    ->firstOrFail();

                // Validate returned quantity doesn't exceed borrowed quantity
                $alreadyReturned = $item->returned_quantity ?? 0;
                $totalToReturn = $alreadyReturned + $itemData['returned_quantity'];
                
                if ($totalToReturn > $item->quantity) {
                    throw new \Exception("Cannot return more than borrowed quantity for {$item->equipment->name}. Borrowed: {$item->quantity}, Already returned: {$alreadyReturned}, Attempting to return: {$itemData['returned_quantity']}");
                }

                $item->update([
                    'returned_quantity' => $totalToReturn,
                    'condition_at_return' => $itemData['condition'] ?? 'good',
                    'damage_notes' => $itemData['damage_notes'] ?? null,
                ]);

                // If damaged or lost, create maintenance request
                if ($itemData['condition'] === 'damaged' || $itemData['condition'] === 'lost') {
                    $this->createDamageReport($item, $itemData['damage_notes'] ?? null);
                }
            }

            // Check if all items are fully returned
            $allReturned = $request->items->every(function ($item) {
                return $item->returned_quantity >= $item->quantity;
            });

            if ($allReturned) {
                $request->update([
                    'status' => 'returned',
                    'returned_at' => now(),
                    'returned_by' => $staff->id,
                ]);
            }

            Log::info('Equipment borrow request items returned', [
                'request_id' => $request->id,
                'returned_by' => $staff->id,
            ]);

            return $request->fresh();
        });
    }

    private function createDamageReport(EquipmentBorrowItem $item, ?string $notes): void
    {
        // Create maintenance request for damaged equipment
        // This would integrate with Module 8: Maintenance
        Log::warning('Equipment damage reported', [
            'item_id' => $item->id,
            'equipment_id' => $item->equipment_id,
            'notes' => $notes,
        ]);
    }

    private function generateQRCode(EquipmentBorrowRequest $request): string
    {
        $data = json_encode([
            'request_id' => $request->id,
            'user_id' => $request->user_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        $fileName = 'qr-codes/borrow-requests/' . $request->id . '.png';
        $path = public_path($fileName);
        
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        QrCode::format('png')
            ->size(300)
            ->margin(10)
            ->generate($data, $path);

        return $fileName;
    }

    public function getUserBorrowRequests(User $user, $status = null)
    {
        $query = EquipmentBorrowRequest::with(['items.equipment', 'organization'])
            ->where('user_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getAllBorrowRequests($filters = [])
    {
        $query = EquipmentBorrowRequest::with(['items.equipment', 'user', 'organization']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('start_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('end_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getAvailableEquipment($startDate, $endDate)
    {
        return Equipment::where('availability_status', 'Available')
            ->where('available_for_borrowing', true)
            ->get()
            ->filter(function ($equipment) {
                return $equipment->getAvailableQuantityAttribute() > 0;
            });
    }
}
