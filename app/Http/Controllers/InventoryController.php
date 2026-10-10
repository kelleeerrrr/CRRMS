<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventory;
use App\Http\Requests\UpdateInventory;
use App\Models\Inventory;
use App\Models\InventoryTransfer;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        // $this->authorize('viewAny', Inventory::class);

        // Get facilities, equipment, and resources for comprehensive inventory view
        $facilities = \App\Models\Facility::with(['room'])->get();
        $equipment = \App\Models\Equipment::with(['category'])->get();
        $resources = \App\Models\Resource::with(['owner'])->get();

        return view('inventory.index', compact('facilities', 'equipment', 'resources'));
    }

    public function create()
    {
        $this->authorize('create', Inventory::class);

        $equipment = \App\Models\Equipment::where('is_active', true)->get();

        return view('inventory.create', compact('equipment'));
    }

    public function store(StoreInventory $request)
    {
        $this->authorize('create', Inventory::class);

        try {
            $inventory = $this->inventoryService->createInventory(
                $request->validated(),
                Auth::user()
            );

            return redirect()
                ->route('inventory.show', $inventory)
                ->with('success', 'Inventory record created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Inventory $inventory)
    {
        $this->authorize('view', $inventory);

        $inventory->load(['equipment', 'countedBy']);

        return view('inventory.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $this->authorize('update', $inventory);

        $inventory->load(['equipment']);
        $equipment = \App\Models\Equipment::where('is_active', true)->get();

        return view('inventory.edit', compact('inventory', 'equipment'));
    }

    public function update(UpdateInventory $request, Inventory $inventory)
    {
        $this->authorize('update', $inventory);

        try {
            $inventory = $this->inventoryService->updateInventory(
                $inventory,
                $request->validated()
            );

            return redirect()
                ->route('inventory.show', $inventory)
                ->with('success', 'Inventory record updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Inventory $inventory)
    {
        $this->authorize('delete', $inventory);

        try {
            $inventory->delete();

            return redirect()
                ->route('inventory.index')
                ->with('success', 'Inventory record deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function stockCount(Request $request, Inventory $inventory)
    {
        $this->authorize('update', $inventory);

        $request->validate([
            'actual_quantity' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $inventory = $this->inventoryService->performStockCount(
                $inventory,
                $request->only('actual_quantity'),
                Auth::user()
            );

            return redirect()
                ->route('inventory.show', $inventory)
                ->with('success', 'Stock count completed successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function transfers(Request $request)
    {
        $this->authorize('viewAny', InventoryTransfer::class);

        $filters = $request->only(['status', 'from_location', 'to_location']);
        $transfers = $this->inventoryService->getInventoryTransfers($filters);

        return view('inventory.transfers', compact('transfers'));
    }

    public function createTransfer(Inventory $inventory)
    {
        $this->authorize('create', InventoryTransfer::class);

        return view('inventory.create-transfer', compact('inventory'));
    }

    public function storeTransfer(Request $request, Inventory $inventory)
    {
        $this->authorize('create', InventoryTransfer::class);

        $request->validate([
            'to_location' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', "max:{$inventory->quantity}"],
            'transfer_date' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $transfer = $this->inventoryService->transferInventory(
                $inventory,
                $request->only('to_location', 'quantity', 'transfer_date', 'notes'),
                Auth::user()
            );

            return redirect()
                ->route('inventory.transfers')
                ->with('success', 'Transfer created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function completeTransfer(InventoryTransfer $transfer)
    {
        $this->authorize('update', $transfer);

        try {
            $transfer = $this->inventoryService->completeTransfer($transfer, Auth::user());

            return redirect()
                ->route('inventory.transfers')
                ->with('success', 'Transfer completed successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function cancelTransfer(InventoryTransfer $transfer)
    {
        $this->authorize('update', $transfer);

        try {
            $transfer = $this->inventoryService->cancelTransfer($transfer, Auth::user());

            return redirect()
                ->route('inventory.transfers')
                ->with('success', 'Transfer cancelled successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
