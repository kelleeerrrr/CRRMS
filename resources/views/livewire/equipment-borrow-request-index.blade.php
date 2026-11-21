<div>
    <!-- Filters -->
    <x-card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input
                type="search"
                name="search"
                wire:model.live.debounce.200ms="search"
                placeholder="Search by borrower or equipment..."
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
                    'rejected' => 'Rejected',
                    'in_use' => 'In Use',
                    'returned' => 'Returned',
                    'completed' => 'Completed',
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

    <!-- Flash Messages -->
    @if(session('success'))
        <x-toast type="success" :message="session('success')" />
    @endif

    @if(session('error'))
        <x-toast type="error" :message="session('error')" />
    @endif

    <!-- Requests Table -->
    @if($requests->count() > 0)
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
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Equipment</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Borrow Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Return Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            @php
                                $firstItem = $request->items->first();
                            @endphp
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    <div class="whitespace-nowrap">
                                        <a href="{{ route('equipment.borrow-requests.show', $request) }}" class="font-semibold text-blue-700 hover:underline dark:text-blue-400">View details</a>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $request->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-semibold mr-2">
                                            {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $request->user->email }}</p>
                                        </div>
                                    </div>
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
                                        @if(auth()->user()->can('approve', $request) && $request->status === 'pending')
                                            <button wire:click="approve('{{ $request->id }}')" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm">
                                                Approve
                                            </button>
                                            <button wire:click="openRejectModal('{{ $request->id }}')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
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
                {{ $requests->links() }}
            </div>
        </x-card>
    @else
        <x-card>
            <div class="text-center py-12">
                <x-heroicon-o-document class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No borrow requests found</h3>
                <p class="text-gray-600 dark:text-gray-400">There are no equipment borrow requests to display.</p>
            </div>
        </x-card>
    @endif

    <!-- Reject Modal -->
    <x-modal name="reject-modal" :show="false">
        <form wire:submit="reject">
            @csrf
            <input type="hidden" name="requestId" x-model="$dispatch('open-reject-modal').requestId">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reject Borrow Request</h3>
                <x-textarea
                    name="reason"
                    label="Reason for Rejection"
                    placeholder="Please provide a reason for rejecting this request..."
                    rows="4"
                    required
                    wire:model="rejectReason"
                />
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'reject-modal')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Reject Request
                </button>
            </div>
        </form>
    </x-modal>
</div>
