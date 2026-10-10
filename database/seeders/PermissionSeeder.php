<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Facilities
            ['id' => Str::uuid(), 'name' => 'view_facilities', 'display_name' => 'View Facilities', 'description' => 'View facilities and availability', 'module' => 'facilities'],
            ['id' => Str::uuid(), 'name' => 'manage_facilities', 'display_name' => 'Manage Facilities', 'description' => 'Add/edit/deactivate school facilities', 'module' => 'facilities'],
            ['id' => Str::uuid(), 'name' => 'create_reservation', 'display_name' => 'Create Reservation', 'description' => 'Create facility reservations', 'module' => 'facilities'],
            ['id' => Str::uuid(), 'name' => 'manage_own_reservations', 'display_name' => 'Manage Own Reservations', 'description' => 'Edit/cancel own reservations', 'module' => 'facilities'],
            ['id' => Str::uuid(), 'name' => 'approve_reservations', 'display_name' => 'Approve Reservations', 'description' => 'Approve/reject facility reservations', 'module' => 'facilities'],
            ['id' => Str::uuid(), 'name' => 'check_in_out_facilities', 'display_name' => 'Check-in/out Facilities', 'description' => 'Check-in/check-out facilities', 'module' => 'facilities'],

            // Equipment
            ['id' => Str::uuid(), 'name' => 'view_equipment', 'display_name' => 'View Equipment', 'description' => 'View equipment and availability', 'module' => 'equipment'],
            ['id' => Str::uuid(), 'name' => 'manage_equipment', 'display_name' => 'Manage Equipment', 'description' => 'Add/edit school equipment', 'module' => 'equipment'],
            ['id' => Str::uuid(), 'name' => 'create_borrow_request', 'display_name' => 'Create Borrow Request', 'description' => 'Create equipment borrow requests', 'module' => 'equipment'],
            ['id' => Str::uuid(), 'name' => 'manage_own_borrow_requests', 'display_name' => 'Manage Own Borrow Requests', 'description' => 'Edit/cancel own borrow requests', 'module' => 'equipment'],
            ['id' => Str::uuid(), 'name' => 'approve_borrow_requests', 'display_name' => 'Approve Borrow Requests', 'description' => 'Approve/reject equipment borrow requests', 'module' => 'equipment'],
            ['id' => Str::uuid(), 'name' => 'check_out_equipment', 'display_name' => 'Check-out Equipment', 'description' => 'Check-out equipment to users', 'module' => 'equipment'],
            ['id' => Str::uuid(), 'name' => 'return_equipment', 'display_name' => 'Return Equipment', 'description' => 'Record equipment returns', 'module' => 'equipment'],

            // Resources (User-owned lending)
            ['id' => Str::uuid(), 'name' => 'view_resources', 'display_name' => 'View Resources', 'description' => 'View available resources', 'module' => 'resources'],
            ['id' => Str::uuid(), 'name' => 'create_resource_listing', 'display_name' => 'Create Resource Listing', 'description' => 'Post own resources for lending', 'module' => 'resources'],
            ['id' => Str::uuid(), 'name' => 'manage_own_resource_listings', 'display_name' => 'Manage Own Resource Listings', 'description' => 'Edit/delete own resource listings', 'module' => 'resources'],
            ['id' => Str::uuid(), 'name' => 'approve_own_resource_requests', 'display_name' => 'Approve Own Resource Requests', 'description' => 'Approve/reject requests for own resources', 'module' => 'resources'],
            ['id' => Str::uuid(), 'name' => 'manage_school_resources', 'display_name' => 'Manage School Resources', 'description' => 'Add/edit school-owned resources', 'module' => 'resources'],
            ['id' => Str::uuid(), 'name' => 'approve_school_resource_requests', 'display_name' => 'Approve School Resource Requests', 'description' => 'Approve/reject school resource requests', 'module' => 'resources'],

            // Inventory
            ['id' => Str::uuid(), 'name' => 'view_inventory', 'display_name' => 'View Inventory', 'description' => 'View inventory records', 'module' => 'inventory'],
            ['id' => Str::uuid(), 'name' => 'manage_inventory', 'display_name' => 'Manage Inventory', 'description' => 'Update inventory quantities and status', 'module' => 'inventory'],
            ['id' => Str::uuid(), 'name' => 'manage_transfers', 'display_name' => 'Manage Transfers', 'description' => 'Record inventory transfers', 'module' => 'inventory'],

            // Buildings
            ['id' => Str::uuid(), 'name' => 'view_buildings', 'display_name' => 'View Buildings', 'description' => 'View building directory', 'module' => 'buildings'],
            ['id' => Str::uuid(), 'name' => 'manage_buildings', 'display_name' => 'Manage Buildings', 'description' => 'Add/edit buildings, floors, rooms', 'module' => 'buildings'],

            // Maintenance
            ['id' => Str::uuid(), 'name' => 'view_maintenance', 'display_name' => 'View Maintenance', 'description' => 'View maintenance requests', 'module' => 'maintenance'],
            ['id' => Str::uuid(), 'name' => 'create_maintenance_request', 'display_name' => 'Create Maintenance Request', 'description' => 'Create maintenance requests', 'module' => 'maintenance'],
            ['id' => Str::uuid(), 'name' => 'manage_own_maintenance_requests', 'display_name' => 'Manage Own Maintenance Requests', 'description' => 'Edit/cancel own maintenance requests', 'module' => 'maintenance'],
            ['id' => Str::uuid(), 'name' => 'manage_maintenance', 'display_name' => 'Manage Maintenance', 'description' => 'Update maintenance status and assignments', 'module' => 'maintenance'],

            // Service Requests
            ['id' => Str::uuid(), 'name' => 'view_service_requests', 'display_name' => 'View Service Requests', 'description' => 'View service requests', 'module' => 'service'],
            ['id' => Str::uuid(), 'name' => 'create_service_request', 'display_name' => 'Create Service Request', 'description' => 'Create service requests', 'module' => 'service'],
            ['id' => Str::uuid(), 'name' => 'manage_own_service_requests', 'display_name' => 'Manage Own Service Requests', 'description' => 'Edit/cancel own service requests', 'module' => 'service'],
            ['id' => Str::uuid(), 'name' => 'manage_service_requests', 'display_name' => 'Manage Service Requests', 'description' => 'Update service request status', 'module' => 'service'],

            // Reports
            ['id' => Str::uuid(), 'name' => 'view_reports', 'display_name' => 'View Reports', 'description' => 'View system reports', 'module' => 'reports'],
            ['id' => Str::uuid(), 'name' => 'generate_reports', 'display_name' => 'Generate Reports', 'description' => 'Generate and export reports', 'module' => 'reports'],
            ['id' => Str::uuid(), 'name' => 'export_reports', 'display_name' => 'Export Reports', 'description' => 'Export reports to various formats', 'module' => 'reports'],

            // Logs
            ['id' => Str::uuid(), 'name' => 'view_activity_logs', 'display_name' => 'View Activity Logs', 'description' => 'View system activity logs', 'module' => 'logs'],
            ['id' => Str::uuid(), 'name' => 'view_audit_trails', 'display_name' => 'View Audit Trails', 'description' => 'View audit trails', 'module' => 'logs'],

            // Announcements
            ['id' => Str::uuid(), 'name' => 'view_announcements', 'display_name' => 'View Announcements', 'description' => 'View announcements', 'module' => 'announcements'],
            ['id' => Str::uuid(), 'name' => 'manage_announcements', 'display_name' => 'Manage Announcements', 'description' => 'Create/edit/delete announcements', 'module' => 'announcements'],

            // User Management
            ['id' => Str::uuid(), 'name' => 'manage_users', 'display_name' => 'Manage Users', 'description' => 'Create/edit/deactivate users', 'module' => 'users'],
            ['id' => Str::uuid(), 'name' => 'assign_roles', 'display_name' => 'Assign Roles', 'description' => 'Assign roles to users', 'module' => 'users'],
            ['id' => Str::uuid(), 'name' => 'manage_own_profile', 'display_name' => 'Manage Own Profile', 'description' => 'Edit personal profile', 'module' => 'users'],

            // Role & Permission Management
            ['id' => Str::uuid(), 'name' => 'manage_roles', 'display_name' => 'Manage Roles', 'description' => 'Create/edit/delete roles', 'module' => 'authorization'],
            ['id' => Str::uuid(), 'name' => 'manage_permissions', 'display_name' => 'Manage Permissions', 'description' => 'Create/edit permissions', 'module' => 'authorization'],
            ['id' => Str::uuid(), 'name' => 'assign_permissions', 'display_name' => 'Assign Permissions', 'description' => 'Assign permissions to roles', 'module' => 'authorization'],

            // System Configuration
            ['id' => Str::uuid(), 'name' => 'manage_system_config', 'display_name' => 'Manage System Config', 'description' => 'Manage system-wide configuration', 'module' => 'system'],

            // Chat & Notifications
            ['id' => Str::uuid(), 'name' => 'use_chat', 'display_name' => 'Use Chat', 'description' => 'Send and receive messages', 'module' => 'communication'],
            ['id' => Str::uuid(), 'name' => 'view_notifications', 'display_name' => 'View Notifications', 'description' => 'View personal notifications', 'module' => 'communication'],

            // Calendar
            ['id' => Str::uuid(), 'name' => 'view_calendar', 'display_name' => 'View Calendar', 'description' => 'View personal calendar', 'module' => 'calendar'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }
    }
}
