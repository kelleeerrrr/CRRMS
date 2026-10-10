<x-app-layout>
    <x-slot name="title">My Requests</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Requests</h1>
        <p class="text-gray-600 dark:text-gray-400">View your pending requests for facilities, equipment, and resources</p>
    </x-slot>

    <!-- Facility Reservations -->
    <x-card class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Facility Reservations</h2>
        @if($facilityReservations->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Requested</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Facility</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">End Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facilityReservations as $reservation)
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $reservation->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $reservation->facility?->name ?? 'Unknown Facility' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $reservation->start_date ? $reservation->start_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $reservation->end_date ? $reservation->end_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-3 px-4">
                                    <x-status-badge :status="$reservation->status" />
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('facilities.reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                            View
                                        </a>
                                        @if(in_array($reservation->status, ['pending', 'approved']))
                                            <form action="{{ route('facilities.reservations.destroy', $reservation) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this reservation?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No pending facility reservations</p>
        @endif
    </x-card>

    <!-- Equipment Borrow Requests -->
    <x-card class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment Borrow Requests</h2>
        @if($equipmentRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Requested</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Equipment</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Borrow Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Return Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipmentRequests as $request)
                            @php
                                $firstItem = $request->items->first();
                            @endphp
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $firstItem?->equipment?->name ?? 'Unknown' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $firstItem?->quantity ?? 0 }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->borrow_date ? $request->borrow_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->return_date ? $request->return_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-3 px-4">
                                    <x-status-badge :status="$request->status" />
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('equipment.borrow-requests.show', $request) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                            View
                                        </a>
                                        @if(in_array($request->status, ['pending', 'approved']))
                                            <form action="{{ route('equipment.borrow-requests.destroy', $request) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this request?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No pending equipment borrow requests</p>
        @endif
    </x-card>

    <!-- Resource Requests -->
    <x-card>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resource Requests</h2>
        @if($resourceRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Requested</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Resource</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Borrow Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Return Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resourceRequests as $request)
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->resource->name }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->quantity }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->start_date ? $request->start_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->end_date ? $request->end_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-3 px-4">
                                    <x-status-badge :status="$request->status" />
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('resource-borrow-requests.show', $request) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                            View
                                        </a>
                                        @if($request->status === 'pending')
                                            <form action="{{ route('resource-borrow-requests.destroy', $request) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this request?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No pending resource requests</p>
        @endif
    </x-card>
</x-app-layout>
