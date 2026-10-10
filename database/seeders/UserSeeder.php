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
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Student User',
                'email' => 'student@crrms.edu',
                'password' => Hash::make('password'),
                'student_id' => 'ST-2024-001',
                'phone' => '1234567896',
                'status' => 'active',
                'role' => 'student',
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
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            $user->roles()->sync([Role::where('name', $role)->first()->id]);
        }
    }
}
