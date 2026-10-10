<x-slot name="title">{{ $user->name }}</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Users', 'url' => route('users.index')],
        ['title' => $user->name, 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
        </div>
        <div class="flex items-center space-x-3">
            @if(auth()->user()->can('update', $user))
                <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Edit
                </a>
            @endif
            @if(auth()->user()->can('update', $user))
                <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 {{ $user->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-colors">
                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- User Details -->
        <x-card title="User Details">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Full Name</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->email }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Student ID</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->student_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Employee ID</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->employee_id ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        @if($user->is_active)
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-100">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Inactive</span>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Department</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->department->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Organization</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->organization->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Roles -->
        <x-card title="Roles">
            <div class="space-y-3">
                @if($user->roles->count() > 0)
                    @foreach($user->roles as $role)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $role->permissions->count() }} permission(s)</p>
                            </div>
                            @if(auth()->user()->can('update', $user))
                                <form action="{{ route('users.remove-role', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this role?')">
                                    @csrf
                                    <input type="hidden" name="role" value="{{ $role->name }}">
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Remove
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No roles assigned</p>
                @endif
            </div>
            @if(auth()->user()->can('update', $user))
                <div class="mt-4">
                    <a href="{{ route('users.roles', $user) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                        Manage Roles
                    </a>
                </div>
            @endif
        </x-card>

        <!-- Permissions -->
        <x-card title="Direct Permissions">
            <div class="space-y-3">
                @if($user->permissions->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->permissions as $permission)
                            <span class="px-2 py-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-100">
                                {{ $permission->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No direct permissions assigned</p>
                @endif
            </div>
            @if(auth()->user()->can('update', $user))
                <div class="mt-4">
                    <a href="{{ route('users.permissions', $user) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                        Manage Permissions
                    </a>
                </div>
            @endif
        </x-card>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Profile Card -->
        <x-card>
            <div class="text-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-3xl mx-auto mb-4">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
            </div>
        </x-card>

        <!-- Quick Actions -->
        <x-card title="Quick Actions">
            <div class="space-y-2">
                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-3" />
                    Back to Users
                </a>
                @if(auth()->user()->can('delete', $user))
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline w-full" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                            <x-heroicon-o-trash class="w-5 h-5 mr-3" />
                            Delete User
                        </button>
                    </form>
                @endif
            </div>
        </x-card>
    </div>
</div>
