<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Lending</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Resources you've shared</p>
    </x-slot>

    @if($resources->count() > 0)
        <div class="mb-6 flex justify-end">
            <a href="{{ route('resources.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                Lend New Resource
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($resources as $resource)
                <x-card>
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $resource->name }}</h3>
                            @if($resource->category)
                                <span class="inline-block px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full mt-1">
                                    {{ $resource->category }}
                                </span>
                            @endif
                        </div>
                        <x-status-badge :status="$resource->status" />
                    </div>

                    @if($resource->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($resource->description, 100) }}</p>
                    @endif

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-o-check-circle class="w-4 h-4 mr-2" />
                            Condition: {{ ucfirst($resource->condition) }}
                        </div>
                        @if($resource->borrowRequests->count() > 0)
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-users class="w-4 h-4 mr-2" />
                                {{ $resource->borrowRequests->where('status', 'pending')->count() }} pending requests
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('resources.show', $resource) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 text-sm">
                            View Details
                        </a>
                        <div class="flex space-x-2">
                            <a href="{{ route('resources.edit', $resource) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 text-sm">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('resources.destroy', $resource) }}" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 text-sm">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $resources->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <x-heroicon-o-cube class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No resources yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Start sharing your resources with others</p>
                <a href="{{ route('resources.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                    Lend Your First Resource
                </a>
            </div>
        </x-card>
    @endif
</x-app-layout>
