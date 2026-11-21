<x-app-layout>
    <x-slot name="title">School Equipment</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between" data-inventory-header>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">School Equipment</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage school equipment inventory</p>
            </div>
            @if(auth()->user()->hasRole('school_officer'))
                <a href="{{ route('equipment.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                    Add Equipment
                </a>
            @elseif($equipment->isEmpty())
                <a href="{{ route('equipment.borrow-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                    Borrow Equipment
                </a>
            @endif
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form data-inventory-search method="GET" action="{{ route('equipment.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-input
                    type="search"
                    name="search"
                    :value="$search"
                    placeholder="Search equipment..."
                    label="Search"
                />
                
                <x-select
                    name="category_id"
                    :value="$category_id"
                    label="Category"
                    placeholder=""
                    :options="['' => 'All Categories'] + array_combine(
                        $types = ['Computer', 'Projector', 'Audio Equipment', 'Laboratory Equipment', 'Sports Equipment', 'Office Equipment', 'Other'],
                        $types
                    ) + $categories->pluck('name', 'id')->toArray()"
                />
                
                <x-select
                    name="status"
                    placeholder=""
                    :value="$status"
                    label="Status"
                    :options="[
                        '' => 'All Status',
                        'Available' => 'Available',
                        'Unavailable' => 'Unavailable',
                        'Under Maintenance' => 'Under Maintenance',
                        'Retired' => 'Retired'
                    ]"
                />
            </div>
            
            <div class="mt-4 flex justify-end">
                <noscript><x-button type="submit">Search</x-button></noscript>
                <a class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" href="{{ route('equipment.index') }}">Reset</a>
            </div>
        </form>
    </x-card>

    <div data-inventory-results aria-live="polite" aria-busy="false">
        <!-- Equipment Grid -->
        @if($equipment->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($equipment as $item)
                    <x-card>
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $item->name }}</h3>
                                @if($item->equipment_category || $item->category)
                                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full mt-1">
                                        {{ $item->equipment_category ?? $item->category->name }}
                                    </span>
                                @endif
                            </div>
                            <x-status-badge :status="$item->availability_status ?? $item->status" />
                        </div>

                        @if($item->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ \Illuminate\Support\Str::limit($item->description, 100) }}</p>
                        @endif

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <span class="mr-2">📦</span>
                                Total: {{ $item->total_quantity ?? 1 }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <span class="mr-2">✅</span>
                                Available: {{ $item->available_quantity }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <span class="mr-2">🏷️</span>
                                Condition: {{ $item->condition }}
                            </div>
                            @if($item->location_building)
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <span class="mr-2">📍</span>
                                {{ $item->location_building }}
                                @if($item->room_storage) - {{ $item->room_storage }}
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700 space-x-2">
                            @if(auth()->user()->hasRole('school_officer'))
                                <a href="{{ route('equipment.edit', $item) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    Edit
                                </a>
                            @else
                                @if($item->isAvailableForBorrowing())
                                    <a href="{{ route('equipment.borrow-requests.create', ['equipment_id' => $item->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                        Borrow This
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 bg-gray-400 text-white text-sm rounded-lg cursor-not-allowed">
                                        Not Available
                                    </span>
                                @endif
                            @endif
                            @if(!auth()->user()->hasRole('school_officer'))
                                <form action="{{ route('chat.with-owner') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="resource_type" value="equipment">
                                    <input type="hidden" name="resource_id" value="{{ $item->id }}">
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                        Chat
                                    </button>
                                </form>
                            @endif
                        </div>
                    </x-card>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $equipment->links() }}
            </div>
        @else
            <x-card>
                <div class="text-center py-12">
                    <x-heroicon-o-cube class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No equipment available</h3>
                    <p class="text-gray-600 dark:text-gray-400">Check back later for available equipment</p>
                </div>
            </x-card>
        @endif
    </div>
</x-app-layout>
