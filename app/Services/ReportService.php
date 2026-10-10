<?php

namespace App\Services;

use App\Models\FacilityReservation;
use App\Models\EquipmentBorrowRequest;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReservationReportExport;
use App\Exports\EquipmentReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\UsageReportExport;
use App\Exports\DepartmentReportExport;

class ReportService
{
    public function generateReservationReport(array $filters = [])
    {
        $query = FacilityReservation::with(['facility', 'user']);

        if (isset($filters['start_date'])) {
            $query->where('start_time', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('end_time', '<=', $filters['end_date']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['facility_id'])) {
            $query->where('facility_id', $filters['facility_id']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function generateEquipmentReport(array $filters = [])
    {
        $query = EquipmentBorrowRequest::with(['items.equipment', 'user']);

        if (isset($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function generateInventoryReport(array $filters = [])
    {
        $query = Inventory::with(['item', 'location']);

        if (isset($filters['category'])) {
            $query->whereHas('item', function ($q) use ($filters) {
                $q->where('category', $filters['category']);
            });
        }

        if (isset($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (isset($filters['low_stock'])) {
            $query->where('quantity', '<=', DB::raw('min_stock'));
        }

        return $query->orderBy('quantity', 'asc')->get();
    }

    public function generateUsageReport(array $filters = [])
    {
        $startDate = $filters['start_date'] ?? now()->subMonth();
        $endDate = $filters['end_date'] ?? now();

        $reservations = FacilityReservation::whereBetween('start_time', [$startDate, $endDate])
            ->selectRaw('facility_id, COUNT(*) as total_reservations, SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved')
            ->groupBy('facility_id')
            ->with('facility')
            ->get();

        $equipmentBorrows = EquipmentBorrowRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('equipment_id, COUNT(*) as total_borrows, SUM(CASE WHEN status = "returned" THEN 1 ELSE 0 END) as returned')
            ->groupBy('equipment_id')
            ->with('equipment')
            ->get();

        return [
            'reservations' => $reservations,
            'equipment_borrows' => $equipmentBorrows,
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
        ];
    }

    public function generateDepartmentReport(array $filters = [])
    {
        $usersByDepartment = User::selectRaw('department_id, COUNT(*) as total_users')
            ->whereNotNull('department_id')
            ->with('department')
            ->groupBy('department_id')
            ->get();

        $reservationsByDepartment = FacilityReservation::selectRaw('department_id, COUNT(*) as total_reservations')
            ->join('users', 'facility_reservations.user_id', '=', 'users.id')
            ->whereNotNull('users.department_id')
            ->groupBy('users.department_id')
            ->get();

        return [
            'users' => $usersByDepartment,
            'reservations' => $reservationsByDepartment,
        ];
    }

    public function exportReservationReport(array $filters = [])
    {
        $data = $this->generateReservationReport($filters);
        
        Log::info('Reservation report exported', [
            'filters' => $filters,
            'count' => $data->count(),
        ]);

        return Excel::download(new ReservationReportExport($data), 'reservations-report.xlsx');
    }

    public function exportEquipmentReport(array $filters = [])
    {
        $data = $this->generateEquipmentReport($filters);
        
        Log::info('Equipment report exported', [
            'filters' => $filters,
            'count' => $data->count(),
        ]);

        return Excel::download(new EquipmentReportExport($data), 'equipment-report.xlsx');
    }

    public function exportInventoryReport(array $filters = [])
    {
        $data = $this->generateInventoryReport($filters);
        
        Log::info('Inventory report exported', [
            'filters' => $filters,
            'count' => $data->count(),
        ]);

        return Excel::download(new InventoryReportExport($data), 'inventory-report.xlsx');
    }

    public function exportUsageReport(array $filters = [])
    {
        $data = $this->generateUsageReport($filters);
        
        Log::info('Usage report exported', [
            'filters' => $filters,
        ]);

        return Excel::download(new UsageReportExport($data), 'usage-report.xlsx');
    }

    public function exportDepartmentReport(array $filters = [])
    {
        $data = $this->generateDepartmentReport($filters);
        
        Log::info('Department report exported', [
            'filters' => $filters,
        ]);

        return Excel::download(new DepartmentReportExport($data), 'department-report.xlsx');
    }

    public function getReportStatistics(): array
    {
        return [
            'total_reservations' => FacilityReservation::count(),
            'active_reservations' => FacilityReservation::where('status', 'approved')->count(),
            'total_borrows' => EquipmentBorrowRequest::count(),
            'active_borrows' => EquipmentBorrowRequest::where('status', 'borrowed')->count(),
            'total_inventory' => Inventory::sum('quantity'),
            'low_stock_items' => Inventory::where('quantity', '<=', DB::raw('min_stock'))->count(),
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
        ];
    }
}
