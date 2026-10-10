<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdateUser;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $filters = $request->only(['role', 'department_id', 'organization_id', 'search']);
        $users = $this->userService->getAllUsers($filters);
        $departments = \App\Models\Department::all();
        $organizations = \App\Models\Organization::all();
        $roles = \Spatie\Permission\Models\Role::all();

        return view('users.index', compact('users', 'departments', 'organizations', 'roles'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $departments = \App\Models\Department::all();
        $organizations = \App\Models\Organization::all();
        $roles = \Spatie\Permission\Models\Role::all();

        return view('users.create', compact('departments', 'organizations', 'roles'));
    }

    public function store(StoreUser $request)
    {
        $this->authorize('create', User::class);

        try {
            $user = $this->userService->createUser($request->validated());

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load(['department', 'organization', 'roles', 'permissions']);

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $departments = \App\Models\Department::all();
        $organizations = \App\Models\Organization::all();
        $roles = \Spatie\Permission\Models\Role::all();

        return view('users.edit', compact('user', 'departments', 'organizations', 'roles'));
    }

    public function update(UpdateUser $request, User $user)
    {
        $this->authorize('update', $user);

        try {
            $user = $this->userService->updateUser($user, $request->validated());

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        try {
            $this->userService->deleteUser($user);

            return redirect()
                ->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);

        try {
            $user = $this->userService->toggleUserStatus($user);

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'User status updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function permissions(User $user)
    {
        $this->authorize('update', $user);

        $user->load('permissions');
        $allPermissions = \Spatie\Permission\Models\Permission::all();

        return view('users.permissions', compact('user', 'allPermissions'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        try {
            $user = $this->userService->assignPermissions($user, $request->permissions);

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'Permissions updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function roles(User $user)
    {
        $this->authorize('update', $user);

        $user->load('roles');
        $allRoles = \Spatie\Permission\Models\Role::all();

        return view('users.roles', compact('user', 'allRoles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'role' => ['required', 'exists:roles,name'],
        ]);

        try {
            $user = $this->userService->assignRole($user, $request->role);

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function removeRole(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'role' => ['required', 'exists:roles,name'],
        ]);

        try {
            $user = $this->userService->removeRole($user, $request->role);

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'Role removed successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
