<?php

namespace App\Services;

use App\Models\FacilityReservation;
use App\Models\EquipmentBorrowRequest;
use App\Models\Inventory;
use App\Models\Building;
use App\Models\Facility;
use App\Models\Equipment;
use App\Models\User;
use App\Models\Department;
use App\Models\MaintenanceRequest;
use App\Models\ServiceRequest;
use App\Models\Announcement;
use App\Models\Conversation;
use App\Models\CalendarEvent;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class DashboardService
{
    public function getSuperAdminStatistics(): array
    {
        return Cache::remember('super_admin_stats', 300, function () {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'inactive_users' => User::where('status', 'inactive')->count(),
                'total_roles' => \App\Models\Role::count(),
                'total_permissions' => \App\Models\Permission::count(),
                'system_activity' => ActivityLog::whereDate('created_at', today())->count(),
                'overall_reports' => FacilityReservation::count() + EquipmentBorrowRequest::count() + \App\Models\ResourceBorrowRequest::count(),
            ];
        });
    }

    public function getAdminStatistics(): array
    {
        return Cache::remember('admin_stats', 300, function () {
            return [
                'total_reservations' => FacilityReservation::count(),
                'total_borrowing' => EquipmentBorrowRequest::count(),
                'total_lending' => \App\Models\ResourceBorrowRequest::count(),
                'resource_utilization' => round((FacilityReservation::where('status', 'approved')->count() / max(Facility::count(), 1)) * 100),
                'equipment_utilization' => round((EquipmentBorrowRequest::where('status', 'in_use')->count() / max(Equipment::count(), 1)) * 100),
                'facility_utilization' => round((FacilityReservation::where('status', 'approved')->count() / max(Facility::count(), 1)) * 100),
                'maintenance_requests' => MaintenanceRequest::whereIn('status', ['pending', 'in_progress'])->count(),
                'service_requests' => ServiceRequest::whereIn('status', ['pending', 'in_progress'])->count(),
            ];
        });
    }

    public function getSchoolOfficerStatistics(): array
    {
        return Cache::remember('school_officer_stats', 300, function () {
            return [
                'pending_facility_reservations' => FacilityReservation::where('status', 'pending')->count(),
                'pending_equipment_borrows' => EquipmentBorrowRequest::where('status', 'pending')->count(),
                'pending_resource_requests' => \App\Models\ResourceBorrowRequest::where('status', 'pending')->count(),
                'maintenance_requests' => MaintenanceRequest::whereIn('status', ['pending', 'in_progress'])->count(),
                'available_facilities' => Facility::where('status', 'available')->count(),
                'reserved_facilities' => FacilityReservation::where('status', 'approved')->count(),
                'available_equipment' => Equipment::where('status', 'available')->count(),
                'borrowed_equipment' => EquipmentBorrowRequest::where('status', 'in_use')->count(),
            ];
        });
    }

    public function getPropertyCustodianStatistics(): array
    {
        return Cache::remember('property_custodian_stats', 300, function () {
            return [
                'ready_release' => EquipmentBorrowRequest::where('status', 'approved')->whereDate('borrow_date', '<=', today())->count(),
                'due_today' => EquipmentBorrowRequest::where('status', 'in_use')->whereDate('return_date', today())->count(),
                'overdue' => EquipmentBorrowRequest::where('status', 'in_use')->where('return_date', '<', today())->count(),
                'damaged' => Equipment::where('condition', 'damaged')->count(),
                'inventory_status' => Equipment::count(),
            ];
        });
    }

    public function getFacilitiesOfficeStatistics(): array
    {
        return Cache::remember('facilities_office_stats', 300, function () {
            return [
                'today_reservations' => FacilityReservation::whereDate('start_time', today())->count(),
                'building_occupancy' => FacilityReservation::where('status', 'approved')->whereDate('start_time', today())->count(),
                'room_utilization' => FacilityReservation::where('status', 'approved')->count(),
                'upcoming_events' => CalendarEvent::where('event_type', 'event')->where('start_date', '>=', today())->count(),
                'facility_maintenance' => MaintenanceRequest::where('type', 'facility')->whereIn('status', ['pending', 'in_progress'])->count(),
            ];
        });
    }

    public function getMaintenanceStatistics(): array
    {
        return Cache::remember('maintenance_stats', 300, function () {
            return [
                'assigned_repairs' => MaintenanceRequest::where('assigned_to', auth()->id())->whereIn('status', ['pending', 'in_progress'])->count(),
                'open_jobs' => MaintenanceRequest::whereIn('status', ['pending', 'in_progress'])->count(),
                'equipment_maintenance' => MaintenanceRequest::where('type', 'equipment')->whereIn('status', ['pending', 'in_progress'])->count(),
                'facility_maintenance' => MaintenanceRequest::where('type', 'facility')->whereIn('status', ['pending', 'in_progress'])->count(),
            ];
        });
    }

    public function getFacultyStatistics(User $user): array
    {
        return Cache::remember("faculty_stats_{$user->id}", 300, function () use ($user) {
            return [
                'my_reservations' => FacilityReservation::where('user_id', $user->id)->count(),
                'borrowed_equipment' => EquipmentBorrowRequest::where('user_id', $user->id)->where('status', 'in_use')->count(),
                'upcoming_reservations' => FacilityReservation::where('user_id', $user->id)->where('start_time', '>=', now())->count(),
                'announcements' => Announcement::where('audience', 'faculty')->orWhere('audience', 'all')->count(),
                'notifications' => auth()->user()->notifications()->where('is_read', false)->count(),
            ];
        });
    }

    public function getStudentStatistics(User $user): array
    {
        return Cache::remember("student_stats_{$user->id}", 300, function () use ($user) {
            return [
                'upcoming_reservations' => FacilityReservation::where('user_id', $user->id)->where('start_time', '>=', now())->count(),
                'borrowed_equipment' => EquipmentBorrowRequest::where('user_id', $user->id)->where('status', 'in_use')->count(),
                'reservation_history' => FacilityReservation::where('user_id', $user->id)->count(),
                'borrow_history' => EquipmentBorrowRequest::where('user_id', $user->id)->count(),
                'notifications' => auth()->user()->notifications()->where('is_read', false)->count(),
                'announcements' => Announcement::where('audience', 'students')->orWhere('audience', 'all')->count(),
            ];
        });
    }

    public function getOrganizationOfficerStatistics(User $user): array
    {
        return Cache::remember("org_officer_stats_{$user->id}", 300, function () use ($user) {
            return [
                'org_reservations' => FacilityReservation::where('user_id', $user->id)->count(),
                'pending_requests' => EquipmentBorrowRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
                'equipment_borrowed' => EquipmentBorrowRequest::where('user_id', $user->id)->where('status', 'in_use')->count(),
                'upcoming_events' => CalendarEvent::where('event_type', 'event')->where('start_date', '>=', today())->count(),
            ];
        });
    }

    public function getSecurityStatistics(): array
    {
        return Cache::remember('security_stats', 300, function () {
            return [
                'active_reservations' => FacilityReservation::where('status', 'approved')->whereDate('start_time', today())->count(),
                'checkins_today' => FacilityReservation::where('status', 'checked_in')->whereDate('updated_at', today())->count(),
                'facility_access' => ActivityLog::where('action', 'check-in')->whereDate('created_at', today())->count(),
                'todays_events' => CalendarEvent::whereDate('start_date', today())->count(),
            ];
        });
    }

    public function getMonthlyReservations(): array
    {
        return FacilityReservation::selectRaw('TO_CHAR(start_time, \'YYYY-MM\') as month, COUNT(*) as count')
            ->where('start_time', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'x' => $item->month,
                'y' => $item->count,
            ])
            ->toArray();
    }

    public function getMonthlyBorrowRequests(): array
    {
        return EquipmentBorrowRequest::selectRaw('TO_CHAR(created_at, \'YYYY-MM\') as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'x' => $item->month,
                'y' => $item->count,
            ])
            ->toArray();
    }

    public function getEquipmentUsage(): array
    {
        return Equipment::select('equipment.*')
            ->selectRaw('COUNT(equipment_borrow_items.id) as borrow_count')
            ->leftJoin('equipment_borrow_items', 'equipment.id', '=', 'equipment_borrow_items.equipment_id')
            ->groupBy('equipment.id')
            ->orderBy('borrow_count', 'desc')
            ->take(10)
            ->get()
            ->map(fn($item) => [
                'x' => $item->name ?? 'Unknown',
                'y' => $item->borrow_count ?? 0,
            ])
            ->toArray();
    }

    public function getBuildingUtilization(): array
    {
        return Building::select('buildings.*')
            ->selectRaw('COUNT(facility_reservations.id) as reservation_count')
            ->leftJoin('rooms', 'buildings.id', '=', 'rooms.building_id')
            ->leftJoin('facilities', 'rooms.id', '=', 'facilities.room_id')
            ->leftJoin('facility_reservations', 'facilities.id', '=', 'facility_reservations.facility_id')
            ->groupBy('buildings.id')
            ->orderBy('reservation_count', 'desc')
            ->take(10)
            ->get()
            ->map(fn($building) => [
                'x' => $building->name ?? 'Unknown',
                'y' => $building->reservation_count ?? 0,
            ])
            ->toArray();
    }

    public function getUserGrowth(): array
    {
        return User::selectRaw('TO_CHAR(created_at, \'YYYY-MM\') as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($item) => [
                'x' => $item->month,
                'y' => $item->count,
            ])
            ->toArray();
    }

    public function getReservationStatus(): array
    {
        return FacilityReservation::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->map(fn($item) => [
                'x' => ucfirst($item->status),
                'y' => $item->count,
            ])
            ->toArray();
    }

    public function getEquipmentCategories(): array
    {
        return Equipment::selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->map(fn($item) => [
                'x' => 'Category ' . $item->category_id,
                'y' => $item->count,
            ])
            ->toArray();
    }

    public function getSchoolOfficerPendingRequests(int $limit = 10): array
    {
        $facilityReservations = FacilityReservation::with(['facility', 'user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($r) => [
                'type' => 'facility_reservation',
                'id' => $r->id,
                'title' => $r->facility->name,
                'user' => $r->user->name,
                'date' => $r->start_date->format('M j, Y'),
                'status' => $r->status,
                'url' => route('facilities.reservations.show', $r),
            ]);

        $equipmentBorrows = EquipmentBorrowRequest::with(['user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($b) => [
                'type' => 'equipment_borrow',
                'id' => $b->id,
                'title' => 'Equipment Request',
                'user' => $b->user->name,
                'date' => $b->created_at->format('M j, Y'),
                'status' => $b->status,
                'url' => route('equipment.borrow-requests.show', $b),
            ]);

        $resourceBorrows = \App\Models\ResourceBorrowRequest::with(['resource', 'user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($b) => [
                'type' => 'resource_borrow',
                'id' => $b->id,
                'title' => $b->resource->name,
                'user' => $b->user->name,
                'date' => $b->created_at->format('M j, Y'),
                'status' => $b->status,
                'url' => '#',
            ]);

        return $facilityReservations->concat($equipmentBorrows)->concat($resourceBorrows)->take($limit)->toArray();
    }

    public function getSchoolOfficerMaintenance(int $limit = 5): array
    {
        return MaintenanceRequest::with(['requestable', 'assignedTo'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'type' => $m->type,
                'description' => $m->description,
                'status' => $m->status,
                'assigned_to' => $m->assignedTo->name ?? 'Unassigned',
                'created_at' => $m->created_at->diffForHumans(),
                'url' => route('maintenance.show', $m),
            ])
            ->toArray();
    }

    public function getSchoolOfficerResourceStatus(): array
    {
        return [
            'available_facilities' => Facility::where('status', 'available')->count(),
            'available_equipment' => Equipment::where('status', 'available')->count(),
            'borrowed_equipment' => EquipmentBorrowRequest::where('status', 'in_use')->count(),
            'reserved_facilities' => FacilityReservation::where('status', 'approved')->where('start_date', '>=', today())->count(),
            'under_maintenance' => MaintenanceRequest::whereIn('status', ['pending', 'in_progress'])->count(),
        ];
    }

    public function getAdminReservationOverview(): array
    {
        return [
            'total' => FacilityReservation::count(),
            'by_status' => FacilityReservation::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->map(fn($item) => ['x' => ucfirst($item->status), 'y' => $item->count])
                ->toArray(),
            'monthly_trend' => FacilityReservation::selectRaw('TO_CHAR(created_at, \'YYYY-MM\') as month, COUNT(*) as count')
                ->where('created_at', '>=', now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(fn($item) => ['x' => $item->month, 'y' => $item->count])
                ->toArray(),
        ];
    }

    public function getAdminBorrowingLendingOverview(): array
    {
        return [
            'equipment_borrowing' => EquipmentBorrowRequest::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->map(fn($item) => ['x' => ucfirst($item->status), 'y' => $item->count])
                ->toArray(),
            'resource_borrowing' => \App\Models\ResourceBorrowRequest::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->map(fn($item) => ['x' => ucfirst($item->status), 'y' => $item->count])
                ->toArray(),
            'resource_lending' => \App\Models\ResourceBorrowRequest::with('resource')
                ->whereHas('resource', fn($q) => $q->where('owner_type', 'school'))
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->map(fn($item) => ['x' => ucfirst($item->status), 'y' => $item->count])
                ->toArray(),
        ];
    }

    public function getAdminResourceUtilization(): array
    {
        return [
            'most_used_facilities' => Facility::select('facilities.*')
                ->selectRaw('COUNT(facility_reservations.id) as reservation_count')
                ->leftJoin('facility_reservations', 'facilities.id', '=', 'facility_reservations.facility_id')
                ->groupBy('facilities.id')
                ->orderBy('reservation_count', 'desc')
                ->take(5)
                ->get()
                ->map(fn($f) => ['x' => $f->name, 'y' => $f->reservation_count ?? 0])
                ->toArray(),
            'most_borrowed_equipment' => Equipment::select('equipment.*')
                ->selectRaw('COUNT(equipment_borrow_items.id) as borrow_count')
                ->leftJoin('equipment_borrow_items', 'equipment.id', '=', 'equipment_borrow_items.equipment_id')
                ->groupBy('equipment.id')
                ->orderBy('borrow_count', 'desc')
                ->take(5)
                ->get()
                ->map(fn($e) => ['x' => $e->name, 'y' => $e->borrow_count ?? 0])
                ->toArray(),
            'most_borrowed_resources' => \App\Models\Resource::select('resources.*')
                ->selectRaw('COUNT(resource_borrow_requests.id) as borrow_count')
                ->leftJoin('resource_borrow_requests', 'resources.id', '=', 'resource_borrow_requests.resource_id')
                ->groupBy('resources.id')
                ->orderBy('borrow_count', 'desc')
                ->take(5)
                ->get()
                ->map(fn($r) => ['x' => $r->name, 'y' => $r->borrow_count ?? 0])
                ->toArray(),
        ];
    }

    public function getAdminMaintenanceServiceOverview(): array
    {
        return [
            'maintenance' => MaintenanceRequest::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->map(fn($item) => ['x' => ucfirst($item->status), 'y' => $item->count])
                ->toArray(),
            'service' => ServiceRequest::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->map(fn($item) => ['x' => ucfirst($item->status), 'y' => $item->count])
                ->toArray(),
        ];
    }

    public function getSuperAdminUserOverview(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'users_by_role' => User::selectRaw('roles.name as role_name, COUNT(*) as count')
                ->join('role_user', 'users.id', '=', 'role_user.user_id')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->groupBy('roles.name')
                ->get()
                ->map(fn($item) => ['x' => ucfirst(str_replace('_', ' ', $item->role_name)), 'y' => $item->count])
                ->toArray(),
        ];
    }

    public function getSuperAdminRolePermissionOverview(): array
    {
        return [
            'total_roles' => \App\Models\Role::count(),
            'total_permissions' => \App\Models\Permission::count(),
            'roles' => \App\Models\Role::with('permissions')
                ->get()
                ->map(fn($role) => [
                    'name' => $role->display_name,
                    'permissions_count' => $role->permissions->count(),
                ])
                ->toArray(),
        ];
    }

    public function getSuperAdminSystemActivity(int $limit = 10): array
    {
        return ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($log) => [
                'action' => $log->action,
                'description' => $log->description,
                'user' => $log->user->name ?? 'System',
                'time' => $log->created_at->diffForHumans(),
                'type' => $this->categorizeActivity($log->action),
            ])
            ->toArray();
    }

    private function categorizeActivity($action): string
    {
        if (str_contains($action, 'login') || str_contains($action, 'logout')) {
            return 'auth';
        } elseif (str_contains($action, 'user') || str_contains($action, 'role') || str_contains($action, 'permission')) {
            return 'user_management';
        } elseif (str_contains($action, 'config') || str_contains($action, 'setting')) {
            return 'configuration';
        } else {
            return 'general';
        }
    }

    public function getAdminRecentActivity(int $limit = 10): array
    {
        return ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($log) => [
                'action' => $log->action,
                'description' => $log->description,
                'user' => $log->user->name ?? 'System',
                'time' => $log->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function getLatestAnnouncements(int $limit = 5): array
    {
        return Announcement::where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($announcement) => [
                'title' => $announcement->title,
                'category' => $announcement->category,
                'published_at' => $announcement->published_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function getRecentChats(int $limit = 5): array
    {
        return Conversation::with(['participants'])
            ->whereHas('participants', fn($q) => $q->where('user_id', auth()->id()))
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($chat) => [
                'name' => $chat->participants->where('user_id', '!=', auth()->id())->first()->user->name ?? 'Unknown',
                'last_message' => $chat->messages->last()->message ?? 'No messages',
                'updated_at' => $chat->updated_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function getRecentReservations(int $limit = 5): array
    {
        return FacilityReservation::with(['facility', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($reservation) => [
                'id' => $reservation->id,
                'facility' => $reservation->facility->name,
                'user' => $reservation->user->name,
                'start_time' => $reservation->start_date->format('M j, Y') . ' ' . $reservation->start_time->format('g:i A'),
                'status' => $reservation->status,
            ])
            ->toArray();
    }

    public function getRecentBorrowRequests(int $limit = 5): array
    {
        return EquipmentBorrowRequest::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($borrow) => [
                'id' => $borrow->id,
                'user' => $borrow->user->name,
                'borrow_date' => $borrow->borrow_date?->format('M j, Y') ?? 'Not set',
                'return_date' => $borrow->return_date?->format('M j, Y') ?? 'Not set',
                'status' => $borrow->status,
            ])
            ->toArray();
    }

    public function getPendingApprovals(int $limit = 5): array
    {
        $reservations = FacilityReservation::with(['facility', 'user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($r) => [
                'type' => 'reservation',
                'title' => $r->facility->name,
                'user' => $r->user->name,
                'created_at' => $r->created_at->diffForHumans(),
                'url' => route('facilities.reservations.show', $r),
            ]);

        $borrows = EquipmentBorrowRequest::with(['user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($b) => [
                'type' => 'borrow',
                'title' => "Equipment Request",
                'user' => $b->user->name,
                'created_at' => $b->created_at->diffForHumans(),
                'url' => route('equipment.borrow-requests.show', $b),
            ]);

        return $reservations->concat($borrows)->take($limit)->toArray();
    }

    public function getLowInventoryAlerts(int $limit = 5): array
    {
        return Equipment::with(['building', 'room'])
            ->where('condition', 'damaged')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($equipment) => [
                'item' => $equipment->brand . ' ' . $equipment->model,
                'condition' => $equipment->condition,
                'location' => $equipment->building->name ?? 'Unknown',
            ])
            ->toArray();
    }

    public function getUpcomingTasks(User $user, int $limit = 5): array
    {
        $reservations = FacilityReservation::with('facility')
            ->where('user_id', $user->id)
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit($limit)
            ->get()
            ->map(fn($r) => [
                'type' => 'reservation',
                'title' => $r->facility->name,
                'due_at' => $r->start_date->format('M j, Y') . ' ' . $r->start_time->format('g:i A'),
            ]);

        $borrows = EquipmentBorrowRequest::where('user_id', $user->id)
            ->where('status', 'in_use')
            ->where('return_date', '>=', now()->toDateString())
            ->orderBy('return_date', 'asc')
            ->limit($limit)
            ->get()
            ->map(fn($b) => [
                'type' => 'borrow',
                'title' => "Return Equipment",
                'due_at' => $b->return_date->format('M j, Y'),
            ]);

        return $reservations->concat($borrows)->take($limit)->toArray();
    }

    public function getSystemHealth(): array
    {
        return [
            'database' => DB::select('SELECT 1')[0] ? 'healthy' : 'unhealthy',
            'cache' => Cache::put('health_check', true, 10) ? 'healthy' : 'unhealthy',
            'storage' => Storage::disk('local')->exists('/') ? 'healthy' : 'unhealthy',
        ];
    }
}
