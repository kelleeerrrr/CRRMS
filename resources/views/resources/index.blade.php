<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Other Resources</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Browse resources shared by other users</p>
            </div>
            <div class="flex items-center space-x-3">
                @if(auth()->user()->roles()->first()?->name === 'school_officer' || auth()->user()->roles()->first()?->name === 'user')
                    <a href="{{ route('resources.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                        Add Resource
                    </a>
                @endif
                @if(auth()->user()->roles()->first()?->name === 'user')
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                        Borrow Resource
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    @if($resources->count() > 0)
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
                            <x-heroicon-o-user class="w-4 h-4 mr-2" />
                            Owner: {{ $resource->owner ? $resource->owner->name : 'School' }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-o-check-circle class="w-4 h-4 mr-2" />
                            Condition: {{ ucfirst($resource->condition) }}
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        @if($resource->owner_id === auth()->id())
                            <a href="{{ route('resources.edit', $resource) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                Edit
                            </a>
                        @else
                            <a href="{{ route('resources.show', $resource) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 text-sm">
                                View Details
                            </a>
                            @if(auth()->user()->roles()->first()?->name === 'user')
                                <a href="{{ route('resources.show', $resource) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    Request to Borrow
                                </a>
                            @endif
                        @endif
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No resources available</h3>
                <p class="text-gray-600 dark:text-gray-400">Check back later for available resources</p>
            </div>
        </x-card>
    @endif
</x-app-layout>
