<x-slot name="title">{{ $event->title }}</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Calendar', 'url' => route('calendar.index')],
        ['title' => $event->title, 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $event->title }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ ucfirst($event->event_type) }} Event</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('calendar.edit', $event) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Edit
            </a>
            <a href="{{ route('calendar.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Back
            </a>
        </div>
    </div>
</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Event Details -->
        <x-card>
            <div class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    @if($event->event_type === 'reservation')
                        <span class="px-3 py-1 text-sm font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-100">Reservation</span>
                    @elseif($event->event_type === 'borrow')
                        <span class="px-3 py-1 text-sm font-semibold text-purple-800 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-100">Equipment Borrow</span>
                    @elseif($event->event_type === 'maintenance')
                        <span class="px-3 py-1 text-sm font-semibold text-orange-800 bg-orange-100 rounded-full dark:bg-orange-900 dark:text-orange-100">Maintenance</span>
                    @elseif($event->event_type === 'holiday')
                        <span class="px-3 py-1 text-sm font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Holiday</span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-100">Event</span>
                    @endif
                    
                    @if($event->is_all_day)
                        <span class="flex items-center text-blue-600 dark:text-blue-400">
                            <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                            All Day
                        </span>
                    @endif
                </div>
                
                @if($event->description)
                    <div class="prose dark:prose-invert max-w-none">
                        {{ $event->description }}
                    </div>
                @endif
                
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Start Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->start_date->format('F j, Y') }}</p>
                    </div>
                    @if($event->end_date)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">End Date</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->end_date->format('F j, Y') }}</p>
                        </div>
                    @endif
                    @if($event->start_time)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Start Time</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->start_time->format('g:i A') }}</p>
                        </div>
                    @endif
                    @if($event->end_time)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">End Time</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->end_time->format('g:i A') }}</p>
                        </div>
                    @endif
                    @if($event->location)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Location</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->location }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created By</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->createdBy->name }}</p>
                    </div>
                </div>
                
                @if($event->is_recurring)
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Recurrence Pattern</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($event->recurrence_pattern) }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Related Model Info -->
        @if($event->related_model_type && $event->related_model_id)
            <x-card title="Related Record">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <p>Model: {{ $event->related_model_type }}</p>
                    <p>ID: {{ $event->related_model_id }}</p>
                </div>
            </x-card>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <x-card title="Quick Actions">
            <div class="space-y-2">
                <a href="{{ route('calendar.index') }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-3" />
                    Back to Calendar
                </a>
                <form action="{{ route('calendar.destroy', $event) }}" method="POST" class="inline w-full" onsubmit="return confirm('Are you sure you want to delete this event?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                        <x-heroicon-o-trash class="w-5 h-5 mr-3" />
                        Delete
                    </button>
                </form>
            </div>
        </x-card>

        <!-- Event Info -->
        <x-card title="Event Information">
            <div class="space-y-3">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full {{ $event->event_type === 'holiday' ? 'bg-red-100 dark:bg-red-900' : 'bg-blue-100 dark:bg-blue-900' }} flex items-center justify-center mr-3">
                        <x-heroicon-o-calendar class="w-5 h-5 {{ $event->event_type === 'holiday' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400' }}" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($event->event_type) }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Event type</p>
                    </div>
                </div>
                
                @if($event->location)
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center mr-3">
                            <x-heroicon-o-map-pin class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->location }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Location</p>
                        </div>
                    </div>
                @endif
            </div>
        </x-card>
    </div>
</div>
