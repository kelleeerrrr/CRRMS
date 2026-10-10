<x-slot name="title">Manage Permissions - {{ $user->name }}</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Users', 'url' => route('users.index')],
        ['title' => $user->name, 'url' => route('users.show', $user)],
        ['title' => 'Permissions', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manage Permissions</h1>
    <p class="text-gray-600 dark:text-gray-400">Assign direct permissions to {{ $user->name }}</p>
</x-slot>

<x-card>
    <form action="{{ route('users.update-permissions', $user) }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            @foreach($allPermissions->groupBy(function($item) { return explode('-', $item->name)[0]; }) as $group => $permissions)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 capitalize">{{ $group }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($permissions as $permission)
                            <label class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('users.show', $user) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Permissions
            </button>
        </div>
    </form>
</x-card>
