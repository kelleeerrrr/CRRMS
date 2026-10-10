<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Facility Reservations</h2>
    </div>

    <!-- Filters -->
    <x-card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input
                type="text"
                name="search"
                wire:model.live="search"
                placeholder="Search by name, contact, or facility..."
                label="Search"
            />
            
            <x-select
                name="status"
                wire:model.live="status"
                label="Status"
                :options="[
                    '' => 'All Status',
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'cancelled' => 'Cancelled'
                ]"
            />
        </div>
        
        <div class="mt-4 flex justify-end">
            <x-button variant="ghost" wire:click="resetFilters">
                Reset Filters
            </x-button>
        </div>
    </x-card>

    <!-- Reservations Table -->
    @if($reservations->count() > 0)
        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <button wire:click="sortBy('created_at')" class="flex items-center hover:text-gray-900 dark:hover:text-white">
                                    Requested
                                    @if($sortField === 'created_at')
                                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </button>
                            </th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Borrower</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Facility</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">End Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $reservation)
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $reservation->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-semibold mr-2">
                                            {{ strtoupper(substr($reservation->reservee_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reservation->reservee_name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $reservation->contact_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $reservation->facility?->name ?? 'No Facility' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $reservation->start_date->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $reservation->end_date->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    <x-status-badge :status="$reservation->status" />
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('facilities.reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                            View
                                        </a>
                                        @if(auth()->user()->can('approve', $reservation) && $reservation->status === 'pending')
                                            <button wire:click="approve('{{ $reservation->id }}')" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm">
                                                Approve
                                            </button>
                                            <button wire:click="reject('{{ $reservation->id }}')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                Reject
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $reservations->links() }}
            </div>
        </x-card>
    @else
        <x-card>
            <div class="text-center py-12">
                <x-heroicon-o-calendar class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No reservations found</h3>
                <p class="text-gray-600 dark:text-gray-400">Check back later for reservation requests</p>
            </div>
        </x-card>
    @endif
</div>
