<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Get dashboard data based on user role
        $data = $this->getDashboardData($user);
        
        return view('dashboard.index', $data);
    }

    private function getDashboardData($user)
    {
        $role = $user->roles()->first()?->name ?? 'user';

        switch ($role) {
            case 'super_admin':
                return $this->getSuperAdminDashboard();
            case 'admin':
                return $this->getAdminDashboard();
            case 'school_officer':
                return $this->getSchoolOfficerDashboard($user);
            case 'user':
                return $this->getUserDashboard($user);
            default:
                return $this->getUserDashboard($user);
        }
    }

    private function getSuperAdminDashboard()
    {
        return [
            'role' => 'super_admin',
            'stats' => $this->dashboardService->getSuperAdminStatistics(),
            'userOverview' => $this->dashboardService->getSuperAdminUserOverview(),
            'rolePermissionOverview' => $this->dashboardService->getSuperAdminRolePermissionOverview(),
            'systemActivity' => $this->dashboardService->getSuperAdminSystemActivity(),
        ];
    }

    private function getAdminDashboard()
    {
        return [
            'role' => 'admin',
            'stats' => $this->dashboardService->getAdminStatistics(),
            'reservationOverview' => $this->dashboardService->getAdminReservationOverview(),
            'borrowingLendingOverview' => $this->dashboardService->getAdminBorrowingLendingOverview(),
            'resourceUtilization' => $this->dashboardService->getAdminResourceUtilization(),
            'maintenanceServiceOverview' => $this->dashboardService->getAdminMaintenanceServiceOverview(),
            'recentActivity' => $this->dashboardService->getAdminRecentActivity(),
        ];
    }

    private function getSchoolOfficerDashboard($user)
    {
        return [
            'role' => 'school_officer',
            'stats' => $this->dashboardService->getSchoolOfficerStatistics(),
            'pendingRequests' => $this->dashboardService->getSchoolOfficerPendingRequests(),
            'maintenance' => $this->dashboardService->getSchoolOfficerMaintenance(),
            'resourceStatus' => $this->dashboardService->getSchoolOfficerResourceStatus(),
        ];
    }

    private function getUserDashboard($user)
    {
        return [
            'role' => 'user',
            'stats' => $this->dashboardService->getStudentStatistics($user),
            'monthlyActivity' => $this->dashboardService->getMonthlyReservations(),
            'borrowHistory' => $this->dashboardService->getMonthlyBorrowRequests(),
            'upcomingTasks' => $this->dashboardService->getUpcomingTasks($user),
            'latestAnnouncements' => $this->dashboardService->getLatestAnnouncements(),
        ];
    }
}
