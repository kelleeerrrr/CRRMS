<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceLog;
use App\Models\MaintenanceLog;
use App\Models\User;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    protected MaintenanceService $maintenanceService;

    public function __construct(MaintenanceService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['type', 'status', 'priority', 'assigned_to', 'search']);
        $maintenanceLogs = $this->maintenanceService->getMaintenanceLogs($filters);
        $statistics = $this->maintenanceService->getMaintenanceStatistics();
        $technicians = User::whereHas('roles', function ($q) {
            $q->where('name', 'technician');
        })->get();

        return view('maintenance.index', compact('maintenanceLogs', 'statistics', 'technicians'));
    }

    public function create()
    {
        $equipment = \App\Models\Equipment::all();
        $facilities = \App\Models\Facility::all();
        $technicians = User::whereHas('roles', function ($q) {
            $q->where('name', 'technician');
        })->get();

        return view('maintenance.create', compact('equipment', 'facilities', 'technicians'));
    }

    public function store(StoreMaintenanceLog $request)
    {
        try {
            $maintenanceLog = $this->maintenanceService->createMaintenanceLog(
                $request->validated(),
                auth()->user()
            );

            return redirect()
                ->route('maintenance.show', $maintenanceLog)
                ->with('success', 'Maintenance log created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(MaintenanceLog $maintenanceLog)
    {
        $maintenanceLog->load(['maintainable', 'assignedTo', 'createdBy']);

        return view('maintenance.show', compact('maintenanceLog'));
    }

    public function edit(MaintenanceLog $maintenanceLog)
    {
        $maintenanceLog->load('maintainable');
        $equipment = \App\Models\Equipment::all();
        $facilities = \App\Models\Facility::all();
        $technicians = User::whereHas('roles', function ($q) {
            $q->where('name', 'technician');
        })->get();

        return view('maintenance.edit', compact('maintenanceLog', 'equipment', 'facilities', 'technicians'));
    }

    public function update(Request $request, MaintenanceLog $maintenanceLog)
    {
        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'priority' => ['sometimes', 'in:low,medium,high,urgent'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'scheduled_date' => ['nullable', 'date'],
        ]);

        try {
            $maintenanceLog = $this->maintenanceService->updateMaintenanceLog($maintenanceLog, $request->validated());

            return redirect()
                ->route('maintenance.show', $maintenanceLog)
                ->with('success', 'Maintenance log updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(MaintenanceLog $maintenanceLog)
    {
        try {
            $this->maintenanceService->deleteMaintenanceLog($maintenanceLog);

            return redirect()
                ->route('maintenance.index')
                ->with('success', 'Maintenance log deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function complete(Request $request, MaintenanceLog $maintenanceLog)
    {
        $request->validate([
            'completion_notes' => ['nullable', 'string'],
            'cost' => ['nullable', 'numeric'],
        ]);

        try {
            $maintenanceLog = $this->maintenanceService->completeMaintenance($maintenanceLog, $request->validated());

            return redirect()
                ->route('maintenance.show', $maintenanceLog)
                ->with('success', 'Maintenance marked as completed.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, MaintenanceLog $maintenanceLog)
    {
        $request->validate([
            'cancellation_reason' => ['required', 'string'],
        ]);

        try {
            $maintenanceLog = $this->maintenanceService->cancelMaintenance($maintenanceLog, $request->cancellation_reason);

            return redirect()
                ->route('maintenance.show', $maintenanceLog)
                ->with('success', 'Maintenance cancelled successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function assignTechnician(Request $request, MaintenanceLog $maintenanceLog)
    {
        $request->validate([
            'technician_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $technician = User::findOrFail($request->technician_id);
            $maintenanceLog = $this->maintenanceService->assignTechnician($maintenanceLog, $technician);

            return redirect()
                ->route('maintenance.show', $maintenanceLog)
                ->with('success', 'Technician assigned successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
