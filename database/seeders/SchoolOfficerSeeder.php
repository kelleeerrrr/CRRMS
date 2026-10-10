<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolOfficerSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'school_officer')->first();
        
        if (!$role) {
            $role = Role::create([
                'id' => Str::uuid(),
                'name' => 'school_officer',
                'display_name' => 'School Officer',
                'description' => 'Operational management: facilities, equipment, resources, inventory, maintenance, service requests',
            ]);
        }

        $user = User::firstOrCreate(
            ['email' => 'schoolofficer@crrms.edu'],
            [
                'name' => 'School Officer',
                'password' => Hash::make('password'),
                'employee_id' => 'SO-001',
                'phone' => '1234567899',
                'status' => 'active',
            ]
        );

        $user->roles()->sync([$role->id]);
    }
}
