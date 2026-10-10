<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class GateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Super Admin Gates
        Gate::define('super-admin', fn ($user) => $user->hasRole('super_admin'));
        
        // Admin Gates
        Gate::define('admin', fn ($user) => $user->hasRole('admin'));
        
        // Facility Management Gates
        Gate::define('manage-facilities', fn ($user) => $user->hasPermission('manage_facilities'));
        Gate::define('view-facilities', fn ($user) => $user->hasPermission('view_facilities'));
        Gate::define('approve-reservations', fn ($user) => $user->hasPermission('approve_reservations'));
        
        // Equipment Management Gates
        Gate::define('manage-equipment', fn ($user) => $user->hasPermission('manage_equipment'));
        Gate::define('view-equipment', fn ($user) => $user->hasPermission('view_equipment'));
        Gate::define('approve-borrow-requests', fn ($user) => $user->hasPermission('approve_borrow_requests'));
        
        // Building Management Gates
        Gate::define('manage-buildings', fn ($user) => $user->hasPermission('manage_buildings'));
        Gate::define('view-buildings', fn ($user) => $user->hasPermission('view_buildings'));
        
        // User Management Gates
        Gate::define('manage-users', fn ($user) => $user->hasPermission('manage_users'));
        Gate::define('view-users', fn ($user) => $user->hasPermission('view_users'));
        
        // Maintenance Gates
        Gate::define('manage-maintenance', fn ($user) => $user->hasPermission('manage_maintenance'));
        Gate::define('view-maintenance', fn ($user) => $user->hasPermission('view_maintenance'));
        
        // Service Request Gates
        Gate::define('manage-service-requests', fn ($user) => $user->hasPermission('manage_service_requests'));
        Gate::define('view-service-requests', fn ($user) => $user->hasPermission('view_service_requests'));
        
        // Report Gates
        Gate::define('view-reports', fn ($user) => $user->hasPermission('view_reports'));
        Gate::define('generate-reports', fn ($user) => $user->hasPermission('generate_reports'));
        
        // Announcement Gates
        Gate::define('manage-announcements', fn ($user) => $user->hasPermission('manage_announcements'));
        Gate::define('view-announcements', fn ($user) => $user->hasPermission('view_announcements'));
        
        // Activity Log Gates
        Gate::define('view-activity-logs', fn ($user) => $user->hasPermission('view_activity_logs'));
        Gate::define('view-audit-trails', fn ($user) => $user->hasPermission('view_audit_trails'));
    }
}
