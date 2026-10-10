<x-app-layout>
    <x-slot name="title">School Facilities</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">School Facilities</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage school facilities inventory</p>
            </div>
            @if($facilities->count() === 0)
                @if(auth()->user()->roles()->first()?->name === 'school_officer')
                    <a href="{{ route('facilities.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                        Add Facility
                    </a>
                @else
                    <a href="{{ route('facilities.reservations.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                        Reserve Facility
                    </a>
                @endif
            @endif
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('facilities.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    type="text"
                    name="search"
                    :value="$search"
                    placeholder="Search facilities..."
                    label="Search"
                />
                
                <x-select
                    name="status"
                    :value="$status"
                    label="Status"
                    :options="[
                        '' => 'All Status',
                        'available' => 'Available',
                        'maintenance' => 'Maintenance',
                        'unavailable' => 'Unavailable'
                    ]"
                />
            </div>
            
            <div class="mt-4 flex justify-end">
                <x-button variant="ghost" type="submit">Filter</x-button>
                <x-button variant="ghost" type="a" href="{{ route('facilities.index') }}">Reset</x-button>
            </div>
        </form>
    </x-card>

    <!-- Facilities Grid -->
    @if($facilities->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($facilities as $facility)
                <x-card>
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $facility->name }}</h3>
                            @if($facility->room)
                                <span class="inline-block px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full mt-1">
                                    {{ $facility->room->name }}
                                </span>
                            @endif
                        </div>
                        <x-status-badge :status="$facility->status" />
                    </div>

                    @if($facility->location_address)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ \Illuminate\Support\Str::limit($facility->location_address, 100) }}</p>
                    @endif

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-o-users class="w-4 h-4 mr-2" />
                            Capacity: {{ $facility->capacity }} people
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        @if(auth()->user()->roles()->first()?->name === 'school_officer')
                            <a href="#" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                Edit
                            </a>
                        @else
                            <a href="{{ route('facilities.reservations.create', ['facility_id' => $facility->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                Reserve Now
                            </a>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $facilities->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <x-heroicon-o-building-office class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No facilities available</h3>
                <p class="text-gray-600 dark:text-gray-400">Check back later for available facilities</p>
            </div>
        </x-card>
    @endif
</x-app-layout>
