<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin - System administration only (NOT operational management)
        $superAdmin = Role::where('name', 'super_admin')->first();
        $superAdminPermissions = Permission::whereIn('name', [
            // User Management
            'manage_users', 'assign_roles',
            // Role & Permission Management
            'manage_roles', 'manage_permissions', 'assign_permissions',
            // System Configuration
            'manage_system_config',
            // Reports
            'view_reports', 'generate_reports', 'export_reports',
            // Logs
            'view_activity_logs', 'view_audit_trails',
            // View-only access for monitoring
            'view_facilities', 'view_equipment', 'view_resources', 'view_buildings',
            'view_maintenance', 'view_service_requests', 'view_inventory',
            'view_announcements', 'view_users',
            // Communication
            'use_chat', 'view_notifications',
            // Calendar
            'view_calendar',
            // Profile
            'manage_own_profile',
        ])->get();
        $superAdmin->permissions()->sync($superAdminPermissions->pluck('id'));

        // Admin - Reports, logs, monitoring only (NO operational management)
        $admin = Role::where('name', 'admin')->first();
        $adminPermissions = Permission::whereIn('name', [
            'view_reports', 'generate_reports', 'export_reports',
            'view_activity_logs', 'view_audit_trails',
            'view_facilities', 'view_equipment', 'view_resources', 'view_buildings',
            'view_maintenance', 'view_service_requests', 'view_inventory',
            'view_announcements', 'view_users',
        ])->get();
        $admin->permissions()->sync($adminPermissions->pluck('id'));

        // School Officer - Operational management
        $schoolOfficer = Role::where('name', 'school_officer')->first();
        $schoolOfficerPermissions = Permission::whereIn('name', [
            // Facilities
            'view_facilities', 'manage_facilities', 'approve_reservations', 'check_in_out_facilities',
            // Equipment
            'view_equipment', 'manage_equipment', 'approve_borrow_requests', 'check_out_equipment', 'return_equipment',
            // Resources (school-owned only)
            'view_resources', 'manage_school_resources', 'approve_school_resource_requests',
            // Inventory
            'view_inventory', 'manage_inventory', 'manage_transfers',
            // Buildings
            'view_buildings', 'manage_buildings',
            // Maintenance
            'view_maintenance', 'manage_maintenance',
            // Service Requests
            'view_service_requests', 'manage_service_requests',
            // Announcements
            'view_announcements', 'manage_announcements',
            // Calendar
            'view_calendar',
            // Communication
            'use_chat', 'view_notifications',
        ])->get();
        $schoolOfficer->permissions()->sync($schoolOfficerPermissions->pluck('id'));

        // User - Students and Faculty
        $user = Role::where('name', 'user')->first();
        $userPermissions = Permission::whereIn('name', [
            // Facilities
            'view_facilities', 'create_reservation', 'manage_own_reservations',
            // Equipment
            'view_equipment', 'create_borrow_request', 'manage_own_borrow_requests',
            // Resources (user-owned lending)
            'view_resources', 'create_resource_listing', 'manage_own_resource_listings', 'approve_own_resource_requests',
            // Maintenance
            'view_maintenance', 'create_maintenance_request', 'manage_own_maintenance_requests',
            // Service Requests
            'view_service_requests', 'create_service_request', 'manage_own_service_requests',
            // Buildings
            'view_buildings',
            // Announcements
            'view_announcements',
            // Calendar
            'view_calendar',
            // Communication
            'use_chat', 'view_notifications',
            // Profile
            'manage_own_profile',
        ])->get();
        $user->permissions()->sync($userPermissions->pluck('id'));
    }
}
