<x-app-layout>
    <x-slot name="title">My Borrowing</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Borrowing</h1>
        <p class="text-gray-600 dark:text-gray-400">View your approved facilities, equipment, and resources</p>
    </x-slot>

    <!-- Facility Reservations -->
    <x-card class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Facility Reservations</h2>
        @if($facilityReservations->count() > 0)
            <div class="space-y-4">
                @foreach($facilityReservations as $reservation)
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $reservation->facility?->name ?? 'Unknown Facility' }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $reservation->start_date->format('M d, Y') }} - {{ $reservation->end_date->format('M d, Y') }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $reservation->start_time }} - {{ $reservation->end_time }}
                                </p>
                                @if($reservation->checked_in_at)
                                    <p class="text-sm text-green-600 dark:text-green-400 mt-1">Checked in: {{ $reservation->checked_in_at->format('M d, Y H:i') }}</p>
                                @endif
                            </div>
                            <x-status-badge :status="$reservation->status" />
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No approved facility reservations</p>
        @endif
    </x-card>

    <!-- Equipment Borrow Requests -->
    <x-card class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment Borrowed</h2>
        @if($equipmentRequests->count() > 0)
            <div class="space-y-4">
                @foreach($equipmentRequests as $request)
                    @php
                        $firstItem = $request->items->first();
                    @endphp
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $firstItem?->equipment?->name ?? 'Unknown Equipment' }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Quantity: {{ $firstItem?->quantity ?? 0 }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $request->borrow_date ? $request->borrow_date->format('M d, Y') : 'N/A' }} - {{ $request->return_date ? $request->return_date->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            <x-status-badge :status="$request->status" />
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No borrowed equipment</p>
        @endif
    </x-card>

    <!-- Resource Requests -->
    <x-card>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resources</h2>
        @if($resourceRequests->count() > 0)
            <div class="space-y-4">
                @foreach($resourceRequests as $resource)
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $resource->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ \Illuminate\Support\Str::limit($resource->description, 100) }}
                                </p>
                            </div>
                            <x-status-badge :status="$resource->status" />
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No approved resources</p>
        @endif
    </x-card>
</x-app-layout>
