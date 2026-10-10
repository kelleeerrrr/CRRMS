<x-slot name="title">Building Directory</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Buildings', 'url' => route('buildings.index')],
        ['title' => 'Directory', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Building Directory</h1>
            <p class="text-gray-600 dark:text-gray-400">Browse all buildings, floors, and facilities</p>
        </div>
        <div>
            <select wire:model.live="buildingId" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="">All Buildings</option>
                @foreach($directory as $building)
                    <option value="{{ $building->id }}" {{ $buildingId == $building->id ? 'selected' : '' }}>{{ $building->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</x-slot>

<div class="space-y-6">
    @forelse($directory as $building)
        @if(!$buildingId || $buildingId == $building->id)
            <x-card :title="$building->name">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <x-heroicon-o-map-pin class="w-4 h-4 inline mr-1" />
                        {{ $building->address ?? 'No address' }}
                    </p>
                </div>
                
                @if($building->floors->count() > 0)
                    <div class="space-y-4">
                        @foreach($building->floors as $floor)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">
                                        Floor {{ $floor->floor_number }} {{ $floor->name ? "- {$floor->name}" : '' }}
                                    </h4>
                                    @if($floor->is_active)
                                        <span class="text-xs text-green-600 dark:text-green-400">Active</span>
                                    @else
                                        <span class="text-xs text-red-600 dark:text-red-400">Inactive</span>
                                    @endif
                                </div>
                                
                                @if($floor->rooms->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($floor->rooms as $room)
                                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-white">
                                                            Room {{ $room->room_number }}
                                                        </p>
                                                        @if($room->name)
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $room->name }}</p>
                                                        @endif
                                                    </div>
                                                    @if($room->is_active)
                                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                    @else
                                                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                                    @endif
                                                </div>
                                                
                                                @if($room->facilities->count() > 0)
                                                    <div class="mt-2">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Facilities:</p>
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach($room->facilities as $facility)
                                                                <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-100 rounded">
                                                                    {{ $facility->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No rooms on this floor</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No floors in this building</p>
                @endif
            </x-card>
        @endif
    @empty
        <x-card>
            <x-empty-state title="No buildings found" description="Add buildings to the campus to get started." />
        </x-card>
    @endforelse
</div>
