<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between" data-inventory-header>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Other Resources</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Browse resources shared by other users</p>
            </div>
            <div class="flex items-center space-x-3">
                @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('school_officer') || auth()->user()->hasRole('user'))
                    <a href="{{ route('resources.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                        Add Resource
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form data-inventory-search method="GET" action="{{ route('resources.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    type="search"
                    name="search"
                    :value="$search"
                    placeholder="Search resources..."
                    label="Search"
                />

                <x-select
                    name="status"
                    placeholder=""
                    :value="$status"
                    label="Status"
                    :options="[
                        '' => 'All Status',
                        'available' => 'Available',
                        'borrowed' => 'Borrowed',
                        'maintenance' => 'Maintenance',
                        'unavailable' => 'Unavailable'
                    ]"
                />
            </div>

            <div class="mt-4 flex min-h-9 items-center gap-3">
                <span data-inventory-search-status role="status" class="mr-auto text-sm font-medium text-blue-700 dark:text-blue-300"></span>
                <noscript><x-button type="submit">Search</x-button></noscript>
                <a class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" href="{{ route('resources.index') }}">Reset</a>
            </div>
        </form>
    </x-card>

    <div data-inventory-results aria-live="polite" aria-busy="false">
        @if($resources->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($resources as $resource)
                    <x-card>
                        @if($resource->photos && !empty($resource->photos))
                            <div class="mb-4 rounded-lg overflow-hidden">
                                <img src="{{ asset('storage/' . $resource->photos[0]) }}" alt="{{ $resource->name }}" class="w-full h-48 object-cover">
                            </div>
                        @elseif($resource->photo)
                            <div class="mb-4 rounded-lg overflow-hidden">
                                <img src="{{ asset('storage/' . $resource->photo) }}" alt="{{ $resource->name }}" class="w-full h-48 object-cover">
                            </div>
                        @endif

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
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <x-heroicon-o-cube class="w-4 h-4 mr-2" />
                                Available: {{ $resource->available_quantity }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700 space-x-2">
                            @if($resource->owner_id === auth()->id())
                                <a href="{{ route('resources.edit', $resource) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('resources.toggle-availability', $resource) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 {{ $resource->status === 'available' ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-sm rounded-lg transition-colors">
                                        {{ $resource->status === 'available' ? 'Make Unavailable' : 'Make Available' }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('resources.show', $resource) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 text-sm">
                                    View Details
                                </a>
                                @if(auth()->user()->roles()->first()?->name === 'user')
                                    <a href="{{ route('resource-borrow-requests.create', ['resource_id' => $resource->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                        Borrow
                                    </a>
                                @endif
                            @endif
                            @if($resource->owner_id !== auth()->id())
                                <form action="{{ route('chat.with-owner') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="resource_type" value="resource">
                                    <input type="hidden" name="resource_id" value="{{ $resource->id }}">
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                        Chat
                                    </button>
                                </form>
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
                    <p class="text-gray-600 dark:text-gray-400">{{ $search !== '' || $status !== '' ? 'Try a different search or status.' : 'Check back later for available resources.' }}</p>
                </div>
            </x-card>
        @endif
    </div>
</x-app-layout>
