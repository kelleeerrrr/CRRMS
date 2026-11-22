<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => Str::uuid(),
                'name' => 'Super Admin',
                'email' => 'superadmin@crrms.edu',
                'password' => Hash::make('password'),
                'employee_id' => 'SA-001',
                'phone' => '1234567890',
                'status' => 'active',
                'role' => 'super_admin',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Admin User',
                'email' => 'admin@crrms.edu',
                'password' => Hash::make('password'),
                'employee_id' => 'AD-001',
                'phone' => '1234567891',
                'status' => 'active',
                'role' => 'admin',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'School Officer',
                'email' => 'schoolofficer@crrms.edu',
                'password' => Hash::make('password'),
                'employee_id' => 'SO-001',
                'phone' => '1234567899',
                'status' => 'active',
                'role' => 'school_officer',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Property Custodian',
                'email' => 'custodian@crrms.edu',
                'password' => Hash::make('password'),
                'employee_id' => 'PC-001',
                'phone' => '1234567892',
                'status' => 'active',
                'role' => 'property_custodian',
                'fallback_role' => 'school_officer',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Facilities Office',
                'email' => 'facilities@crrms.edu',
                'password' => Hash::make('password'),
                'employee_id' => 'FO-001',
                'phone' => '1234567893',
                'status' => 'active',
                'role' => 'facilities_office',
                'fallback_role' => 'school_officer',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Maintenance Staff',
                'email' => 'maintenance@crrms.edu',
                'password' => Hash::make('password'),
                'employee_id' => 'MS-001',
                'phone' => '1234567894',
                'status' => 'active',
                'role' => 'maintenance_staff',
                'fallback_role' => 'school_officer',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Faculty Member',
                'email' => 'faculty@crrms.edu',
                'password' => Hash::make('password'),
                'employee_id' => 'FM-001',
                'phone' => '1234567895',
                'status' => 'active',
                'role' => 'faculty',
                'fallback_role' => 'user',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Student User',
                'email' => 'student@crrms.edu',
                'password' => Hash::make('password'),
                'student_id' => 'ST-2024-001',
                'phone' => '1234567896',
                'status' => 'active',
                'role' => 'user',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Organization Officer',
                'email' => 'orgofficer@crrms.edu',
                'password' => Hash::make('password'),
                'student_id' => 'ST-2024-002',
                'phone' => '1234567897',
                'status' => 'active',
                'role' => 'organization_officer',
                'fallback_role' => 'user',
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Security Personnel',
                'email' => 'security@crrms.edu',
                'password' => Hash::make('password'),
                'employee_id' => 'SC-001',
                'phone' => '1234567898',
                'status' => 'active',
                'role' => 'security',
                'fallback_role' => 'school_officer',
            ],
        ];

        foreach ($users as $userData) {
            $role = Role::where('name', $userData['role'])->first();

            if (!$role && isset($userData['fallback_role'])) {
                $role = Role::where('name', $userData['fallback_role'])->first();
            }

            if (!$role) {
                $this->command->warn(
                    "Skipping {$userData['email']}: role {$userData['role']} does not exist."
                );
                continue;
            }

            unset($userData['role'], $userData['fallback_role'], $userData['id']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->roles()->sync([$role->id]);
        }
    }
}
