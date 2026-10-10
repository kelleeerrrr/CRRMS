<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\Inventory;
use App\Models\InventoryTransfer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InventoryService
{
    public function createInventory(array $data, User $user): Inventory
    {
        return DB::transaction(function () use ($data, $user) {
            $inventory = Inventory::create([
                'id' => Str::uuid(),
                'equipment_id' => $data['equipment_id'],
                'quantity' => $data['quantity'],
                'location' => $data['location'],
                'condition' => $data['condition'] ?? 'good',
                'notes' => $data['notes'] ?? null,
                'last_counted_at' => now(),
                'counted_by' => $user->id,
            ]);

            // Update equipment total quantity
            $equipment = Equipment::findOrFail($data['equipment_id']);
            $equipment->increment('total_quantity', $data['quantity']);
            $equipment->increment('available_quantity', $data['quantity']);

            Log::info('Inventory record created', [
                'inventory_id' => $inventory->id,
                'equipment_id' => $data['equipment_id'],
                'user_id' => $user->id,
            ]);

            return $inventory;
        });
    }

    public function updateInventory(Inventory $inventory, array $data): Inventory
    {
        return DB::transaction(function () use ($inventory, $data) {
            $oldQuantity = $inventory->quantity;
            $newQuantity = $data['quantity'] ?? $oldQuantity;
            $difference = $newQuantity - $oldQuantity;

            $inventory->update($data);
            $inventory->update(['last_counted_at' => now()]);

            // Update equipment quantities if quantity changed
            if ($difference !== 0) {
                $equipment = $inventory->equipment;
                $equipment->increment('total_quantity', $difference);
                $equipment->increment('available_quantity', $difference);
            }

            Log::info('Inventory record updated', [
                'inventory_id' => $inventory->id,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
            ]);

            return $inventory->fresh();
        });
    }

    public function transferInventory(Inventory $inventory, array $data, User $user): InventoryTransfer
    {
        return DB::transaction(function () use ($inventory, $data, $user) {
            $transfer = InventoryTransfer::create([
                'id' => Str::uuid(),
                'inventory_id' => $inventory->id,
                'from_location' => $inventory->location,
                'to_location' => $data['to_location'],
                'quantity' => $data['quantity'],
                'transfer_date' => $data['transfer_date'] ?? now(),
                'transferred_by' => $user->id,
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);

            Log::info('Inventory transfer created', [
                'transfer_id' => $transfer->id,
                'inventory_id' => $inventory->id,
                'to_location' => $data['to_location'],
            ]);

            return $transfer;
        });
    }

    public function completeTransfer(InventoryTransfer $transfer, User $user): InventoryTransfer
    {
        return DB::transaction(function () use ($transfer, $user) {
            if ($transfer->status !== 'pending') {
                throw new \Exception('Only pending transfers can be completed.');
            }

            $inventory = $transfer->inventory;
            
            // Update inventory location and quantity
            $inventory->update([
                'location' => $transfer->to_location,
                'quantity' => max(0, $inventory->quantity - $transfer->quantity),
                'last_counted_at' => now(),
            ]);

            // Create new inventory record at destination
            $destinationInventory = Inventory::create([
                'id' => Str::uuid(),
                'equipment_id' => $inventory->equipment_id,
                'quantity' => $transfer->quantity,
                'location' => $transfer->to_location,
                'condition' => $inventory->condition,
                'notes' => 'Transferred from ' . $transfer->from_location,
                'last_counted_at' => now(),
                'counted_by' => $user->id,
            ]);

            $transfer->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completed_by' => $user->id,
            ]);

            Log::info('Inventory transfer completed', [
                'transfer_id' => $transfer->id,
                'completed_by' => $user->id,
            ]);

            return $transfer->fresh();
        });
    }

    public function cancelTransfer(InventoryTransfer $transfer, User $user): InventoryTransfer
    {
        return DB::transaction(function () use ($transfer, $user) {
            if ($transfer->status !== 'pending') {
                throw new \Exception('Only pending transfers can be cancelled.');
            }

            $transfer->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $user->id,
            ]);

            Log::info('Inventory transfer cancelled', [
                'transfer_id' => $transfer->id,
                'cancelled_by' => $user->id,
            ]);

            return $transfer->fresh();
        });
    }

    public function performStockCount(Inventory $inventory, array $data, User $user): Inventory
    {
        return DB::transaction(function () use ($inventory, $data, $user) {
            $actualQuantity = $data['actual_quantity'];
            $difference = $actualQuantity - $inventory->quantity;

            $inventory->update([
                'quantity' => $actualQuantity,
                'last_counted_at' => now(),
                'counted_by' => $user->id,
                'notes' => ($inventory->notes ?? '') . "\n\nStock count on " . now()->format('Y-m-d H:i') . " by " . $user->name . ": Actual: {$actualQuantity}, Expected: {$inventory->quantity}, Difference: {$difference}",
            ]);

            // Update equipment quantities
            $equipment = $inventory->equipment;
            $equipment->increment('total_quantity', $difference);
            $equipment->increment('available_quantity', $difference);

            Log::info('Stock count performed', [
                'inventory_id' => $inventory->id,
                'actual_quantity' => $actualQuantity,
                'expected_quantity' => $inventory->quantity,
                'difference' => $difference,
                'counted_by' => $user->id,
            ]);

            return $inventory->fresh();
        });
    }

    public function getAllInventory($filters = [])
    {
        $query = Inventory::with(['equipment', 'countedBy']);

        if (isset($filters['equipment_id'])) {
            $query->where('equipment_id', $filters['equipment_id']);
        }

        if (isset($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }

        if (isset($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getInventoryTransfers($filters = [])
    {
        $query = InventoryTransfer::with(['inventory.equipment', 'transferredBy', 'completedBy', 'cancelledBy']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_location'])) {
            $query->where('from_location', 'like', '%' . $filters['from_location'] . '%');
        }

        if (isset($filters['to_location'])) {
            $query->where('to_location', 'like', '%' . $filters['to_location'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function getLowStockItems($threshold = 5)
    {
        return Inventory::with(['equipment'])
            ->where('quantity', '<=', $threshold)
            ->orderBy('quantity', 'asc')
            ->get();
    }
}
