<?php

namespace App\Services;

use App\Models\User;
use App\Models\Department;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'student_id' => $data['student_id'] ?? null,
                'employee_id' => $data['employee_id'] ?? null,
                'phone' => $data['phone'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'organization_id' => $data['organization_id'] ?? null,
            ]);

            // Assign role if provided
            if (isset($data['role'])) {
                $user->assignRole($data['role']);
            }

            Log::info('User created', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return $user;
        });
    }

    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Update password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            // Update role if provided
            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

            Log::info('User updated', [
                'user_id' => $user->id,
            ]);

            return $user->fresh();
        });
    }

    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            $user->delete();

            Log::info('User deleted', [
                'user_id' => $user->id,
            ]);

            return true;
        });
    }

    public function getAllUsers($filters = [])
    {
        $query = User::with(['department', 'organization', 'roles']);

        if (isset($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['organization_id'])) {
            $query->where('organization_id', $filters['organization_id']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('student_id', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('employee_id', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('name')->paginate(10);
    }

    public function assignPermissions(User $user, array $permissions): User
    {
        return DB::transaction(function () use ($user, $permissions) {
            $user->syncPermissions($permissions);

            Log::info('Permissions assigned to user', [
                'user_id' => $user->id,
                'permissions' => $permissions,
            ]);

            return $user->fresh();
        });
    }

    public function assignRole(User $user, string $role): User
    {
        return DB::transaction(function () use ($user, $role) {
            $user->assignRole($role);

            Log::info('Role assigned to user', [
                'user_id' => $user->id,
                'role' => $role,
            ]);

            return $user->fresh();
        });
    }

    public function removeRole(User $user, string $role): User
    {
        return DB::transaction(function () use ($user, $role) {
            $user->removeRole($role);

            Log::info('Role removed from user', [
                'user_id' => $user->id,
                'role' => $role,
            ]);

            return $user->fresh();
        });
    }

    public function toggleUserStatus(User $user): User
    {
        return DB::transaction(function () use ($user) {
            $user->update(['is_active' => !$user->is_active]);

            Log::info('User status toggled', [
                'user_id' => $user->id,
                'is_active' => $user->is_active,
            ]);

            return $user->fresh();
        });
    }
}
