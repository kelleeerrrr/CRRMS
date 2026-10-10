<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $statistics = $this->reportService->getReportStatistics();

        return view('reports.index', compact('statistics'));
    }

    public function reservations(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'status', 'user_id', 'facility_id']);
        $data = $this->reportService->generateReservationReport($filters);
        $users = \App\Models\User::all();
        $facilities = \App\Models\Facility::all();

        return view('reports.reservations', compact('data', 'users', 'facilities', 'filters'));
    }

    public function exportReservations(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'status', 'user_id', 'facility_id']);
        return $this->reportService->exportReservationReport($filters);
    }

    public function equipment(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'status', 'user_id']);
        $data = $this->reportService->generateEquipmentReport($filters);
        $users = \App\Models\User::all();

        return view('reports.equipment', compact('data', 'users', 'filters'));
    }

    public function exportEquipment(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'status', 'user_id']);
        return $this->reportService->exportEquipmentReport($filters);
    }

    public function inventory(Request $request)
    {
        $filters = $request->only(['category', 'location_id', 'low_stock']);
        $data = $this->reportService->generateInventoryReport($filters);
        $locations = \App\Models\Location::all();

        return view('reports.inventory', compact('data', 'locations', 'filters'));
    }

    public function exportInventory(Request $request)
    {
        $filters = $request->only(['category', 'location_id', 'low_stock']);
        return $this->reportService->exportInventoryReport($filters);
    }

    public function usage(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date']);
        $data = $this->reportService->generateUsageReport($filters);

        return view('reports.usage', compact('data', 'filters'));
    }

    public function exportUsage(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date']);
        return $this->reportService->exportUsageReport($filters);
    }

    public function department(Request $request)
    {
        $filters = $request->only(['department_id']);
        $data = $this->reportService->generateDepartmentReport($filters);
        $departments = \App\Models\Department::all();

        return view('reports.department', compact('data', 'departments', 'filters'));
    }

    public function exportDepartment(Request $request)
    {
        $filters = $request->only(['department_id']);
        return $this->reportService->exportDepartmentReport($filters);
    }
}
