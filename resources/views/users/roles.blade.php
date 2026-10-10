<x-slot name="title">Manage Roles - {{ $user->name }}</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Users', 'url' => route('users.index')],
        ['title' => $user->name, 'url' => route('users.show', $user)],
        ['title' => 'Roles', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manage Roles</h1>
    <p class="text-gray-600 dark:text-gray-400">Assign roles to {{ $user->name }}</p>
</x-slot>

<x-card>
    <form action="{{ route('users.update-role', $user) }}" method="POST">
        @csrf
        
        <div class="space-y-4">
            @foreach($allRoles as $role)
                <label class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                    <input type="radio" name="role" value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <div class="ml-3">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $role->permissions->count() }} permission(s)</p>
                    </div>
                </label>
            @endforeach
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('users.show', $user) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Role
            </button>
        </div>
    </form>
</x-card>
