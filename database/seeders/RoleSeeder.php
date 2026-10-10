<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => Str::uuid(),
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'System-level administration: users, roles, permissions, system configuration',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Monitoring and reporting: view reports, analytics, activity logs, audit trails',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'school_officer',
                'display_name' => 'School Officer',
                'description' => 'Operational management: facilities, equipment, resources, inventory, maintenance, service requests',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'user',
                'display_name' => 'User',
                'description' => 'Students and faculty: reservations, borrowing, lending, service/maintenance requests',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                [
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                ]
            );
        }
    }
}
