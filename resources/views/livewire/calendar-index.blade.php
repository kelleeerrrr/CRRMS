<div>
    <!-- Filters -->
    <x-card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-input
                type="text"
                wire:model.live="search"
                placeholder="Search events..."
                label="Search"
            />
            
            <x-select
                wire:model.live="event_type"
                label="Event Type"
                :options="[
                    '' => 'All Types',
                    'reservation' => 'Reservation',
                    'borrow' => 'Equipment Borrow',
                    'maintenance' => 'Maintenance',
                    'event' => 'Event',
                    'holiday' => 'Holiday'
                ]"
            />
            
            <x-select
                wire:model.live="related_model_type"
                label="Related Model"
                :options="[
                    '' => 'All Models',
                    'App\\Models\\FacilityReservation' => 'Facility Reservation',
                    'App\\Models\\EquipmentBorrowRequest' => 'Equipment Borrow',
                    'App\\Models\\MaintenanceLog' => 'Maintenance Log'
                ]"
            />
        </div>
        
        <div class="mt-4 flex justify-end">
            <x-button variant="ghost" wire:click="resetFilters">
                Reset Filters
            </x-button>
        </div>
    </x-card>

    <!-- Events Table -->
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Events ({{ $events->total() }})
            </h3>
            <a href="{{ route('calendar.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                Add Event
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('title')">
                            Title
                            @if($sortField === 'title')
                                <x-heroicon-o-chevron-up class="w-4 h-4 inline" x-show="{{ $sortDirection === 'asc' }}" />
                                <x-heroicon-o-chevron-down class="w-4 h-4 inline" x-show="{{ $sortDirection === 'desc' }}" />
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('start_date')">
                            Start Date
                            @if($sortField === 'start_date')
                                <x-heroicon-o-chevron-up class="w-4 h-4 inline" x-show="{{ $sortDirection === 'asc' }}" />
                                <x-heroicon-o-chevron-down class="w-4 h-4 inline" x-show="{{ $sortDirection === 'desc' }}" />
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->title }}</div>
                                @if($event->description)
                                    <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ Str::limit($event->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $event->start_date->format('M j, Y') }}
                                @if($event->start_time)
                                    {{ $event->start_time->format('g:i A') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($event->event_type === 'reservation')
                                    <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-100">Reservation</span>
                                @elseif($event->event_type === 'borrow')
                                    <span class="px-2 py-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-100">Borrow</span>
                                @elseif($event->event_type === 'maintenance')
                                    <span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full dark:bg-orange-900 dark:text-orange-100">Maintenance</span>
                                @elseif($event->event_type === 'holiday')
                                    <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Holiday</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-100">Event</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $event->location ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('calendar.show', $event) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                    View
                                </a>
                                <a href="{{ route('calendar.edit', $event) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <x-empty-state title="No events found" description="Create a calendar event to get started." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($events->hasPages())
            <div class="mt-6">
                {{ $events->links() }}
            </div>
        @endif
    </x-card>
</div>
