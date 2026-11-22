<div>
    <!-- Filters -->
    <x-card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input
                type="search"
                name="search"
                wire:model.live.debounce.200ms="search"
                placeholder="Search by name, contact, or facility..."
                label="Search"
            />
            
            <x-select
                name="status"
                wire:model.live="status"
                placeholder=""
                label="Status"
                :options="[
                    '' => 'All Status',
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'reserved' => 'Reserved',
                    'checked_in' => 'Checked In',
                    'completed' => 'Completed',
                    'rejected' => 'Rejected',
                    'cancelled' => 'Cancelled'
                ]"
            />
        </div>
        
                <div class="mt-4 flex min-h-9 items-center justify-between gap-3">
            <div role="status" class="mr-auto text-sm font-medium text-blue-700 dark:text-blue-300">
                <span wire:dirty wire:target="search,status" style="display: none">Searching…</span>
            </div>
            <x-button variant="ghost" wire:click="resetFilters">
                Reset
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
                            <th scope="col" class="text-center py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                            <th scope="col" class="text-center py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Details</th>
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
                                    <div class="flex items-center justify-center gap-2">
                                        @if(auth()->user()->can('approve', $reservation) && $reservation->status === 'pending')
                                            <button wire:click="approve('{{ $reservation->id }}')" class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 hover:bg-green-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-green-600 dark:bg-green-900/40 dark:text-green-300 dark:hover:bg-green-900/70">
                                                Approve
                                            </button>
                                            <button wire:click="reject('{{ $reservation->id }}')" class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800 hover:bg-red-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-red-600 dark:bg-red-900/40 dark:text-red-300 dark:hover:bg-red-900/70">
                                                Reject
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('facilities.reservations.show', $reservation) }}"
                                        class="inline-flex items-center rounded-lg border border-blue-600 px-3 py-1.5 text-sm font-semibold text-blue-700 hover:bg-blue-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-blue-500 dark:text-blue-300 dark:hover:bg-blue-950">
                                        View
                                    </a>
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
