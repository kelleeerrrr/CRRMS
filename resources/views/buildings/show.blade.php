<x-slot name="title">{{ $building->name }}</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Buildings', 'url' => route('buildings.index')],
        ['title' => $building->name, 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $building->name }}</h1>
            <p class="text-gray-600 dark:text-gray-400">Building Code: {{ $building->code }}</p>
        </div>
        <div class="flex items-center space-x-3">
            @if(auth()->user()->can('update', $building))
                <a href="{{ route('buildings.edit', $building) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Edit
                </a>
            @endif
            @if(auth()->user()->can('create', \App\Models\Floor::class))
                <a href="{{ route('buildings.floors.create', $building) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Add Floor
                </a>
            @endif
        </div>
    </div>
</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-3 space-y-6">
        <!-- Building Details -->
        <x-card title="Building Details">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Building Name</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $building->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Building Code</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $building->code }}</p>
                    </div>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $building->address ?? 'N/A' }}</p>
                </div>
                
                @if($building->description)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $building->description }}</p>
                    </div>
                @endif
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Floors</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $building->total_floors }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        @if($building->is_active)
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-100">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Floors -->
        <x-card title="Floors">
            @if($building->floors->count() > 0)
                <div class="space-y-4">
                    @foreach($building->floors as $floor)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        Floor {{ $floor->floor_number }} {{ $floor->name ? "- {$floor->name}" : '' }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $floor->rooms->count() }} room(s)
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if(auth()->user()->can('create', \App\Models\Room::class))
                                        <a href="{{ route('buildings.rooms.create', [$building, $floor]) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            Add Room
                                        </a>
                                    @endif
                                    @if(auth()->user()->can('update', $floor))
                                        <a href="{{ route('buildings.floors.edit', [$building, $floor]) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            Edit
                                        </a>
                                    @endif
                                    @if(auth()->user()->can('delete', $floor))
                                        <form action="{{ route('buildings.floors.delete', [$building, $floor]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this floor?')">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            
                            @if($floor->rooms->count() > 0)
                                <div class="mt-3 pl-4 border-l-2 border-gray-300 dark:border-gray-600">
                                    <div class="space-y-2">
                                        @foreach($floor->rooms as $room)
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-700 dark:text-gray-300">
                                                    Room {{ $room->room_number }} {{ $room->name ? "- {$room->name}" : '' }}
                                                    ({{ $room->facilities->count() }} facility/facilities)
                                                </span>
                                                @if(auth()->user()->can('update', $room))
                                                    <a href="{{ route('buildings.rooms.edit', [$building, $floor, $room]) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        Edit
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state title="No floors found" description="Add floors to this building to get started." />
            @endif
        </x-card>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Statistics -->
        <x-card title="Statistics">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Floors</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['total_floors'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Rooms</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['total_rooms'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Facilities</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['total_facilities'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Active Facilities</span>
                    <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ $stats['active_facilities'] }}</span>
                </div>
            </div>
        </x-card>

        <!-- Quick Actions -->
        <x-card title="Quick Actions">
            <div class="space-y-2">
                <a href="{{ route('buildings.index') }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-3" />
                    Back to Buildings
                </a>
                <a href="{{ route('buildings.directory') }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <x-heroicon-o-map class="w-5 h-5 mr-3" />
                    Building Directory
                </a>
            </div>
        </x-card>
    </div>
</div>
