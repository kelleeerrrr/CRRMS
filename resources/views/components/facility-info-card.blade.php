@props(['facility' => null, 'reservation' => null])

@if($facility)
<div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-4 cursor-pointer hover:shadow-md transition-shadow">
    <div class="flex items-start space-x-4">
        @if($facility->photos && !empty($facility->photos))
            <img src="{{ asset('storage/' . $facility->photos[0]) }}" alt="{{ $facility->name }}" class="w-16 h-16 rounded-lg object-cover">
        @else
            <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white font-bold text-xl">
                {{ strtoupper(substr($facility->name, 0, 2)) }}
            </div>
        @endif
        
        <div class="flex-1">
            <h3 class="font-semibold text-gray-900 dark:text-white text-lg">{{ $facility->name }}</h3>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 space-y-1">
                @if($facility->type)
                    <p><span class="font-medium">Type:</span> {{ ucfirst($facility->type) }}</p>
                @endif
                @if($facility->building)
                    <p><span class="font-medium">Building:</span> {{ $facility->building->name }}</p>
                @endif
                @if($facility->room)
                    <p><span class="font-medium">Room:</span> {{ $facility->room->name }}</p>
                @endif
                @if($facility->floor)
                    <p><span class="font-medium">Floor:</span> {{ $facility->floor->name }}</p>
                @endif
                @if($facility->capacity)
                    <p><span class="font-medium">Capacity:</span> {{ $facility->capacity }} people</p>
                @endif
            </div>
            
            @if($reservation)
                <div class="mt-3 pt-3 border-t border-red-200 dark:border-red-700">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Reservation Status</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <x-status-badge :status="$reservation->status" />
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $reservation->start_date ? $reservation->start_date->format('M d, Y') : 'N/A' }} - 
                            {{ $reservation->end_date ? $reservation->end_date->format('M d, Y') : 'N/A' }}
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="flex items-center justify-end space-x-2 mt-3">
        @if($reservation)
            <a href="{{ route('facilities.reservations.show', $reservation) }}" class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">
                View Reservation
            </a>
        @endif
    </div>
</div>
@endif
