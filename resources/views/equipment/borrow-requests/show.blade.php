<x-app-layout>
    <x-slot name="title">Borrow Request Details</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $borrowRequest->purpose }}</h1>
                <p class="text-gray-600 dark:text-gray-400">Request ID: {{ $borrowRequest->id }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('my-lending.requests') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Back to My Requests
                </a>
            @if(auth()->user()->can('update', $borrowRequest) && $borrowRequest->status === 'pending')
                <a href="{{ route('equipment.borrow-requests.edit', $borrowRequest) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Edit
                </a>
            @endif
            @if(auth()->user()->can('approve', $borrowRequest) && $borrowRequest->status === 'pending')
                <form action="{{ route('equipment.borrow-requests.approve', $borrowRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Approve
                    </button>
                </form>
                <button x-on:click="openRejectModal = true" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Reject
                </button>
            @endif
            @if(auth()->user()->can('delete', $borrowRequest) && in_array($borrowRequest->status, ['pending', 'approved']))
                <form action="{{ route('equipment.borrow-requests.destroy', $borrowRequest) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this request?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Cancel
                    </button>
                </form>
            @endif
            @if(auth()->user()->can('checkOut', $borrowRequest) && $borrowRequest->status === 'approved')
                <form action="{{ route('equipment.borrow-requests.check-out', $borrowRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Check Out
                    </button>
                </form>
            @endif
            @if(auth()->user()->can('return', $borrowRequest) && $borrowRequest->status === 'in_use')
                <button x-on:click="openReturnModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Return Equipment
                </button>
            @endif
        </div>
    </div>
</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Request Details -->
        <x-card title="Request Details">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        <x-status-badge :status="$borrowRequest->status" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Request ID</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $borrowRequest->id }}</p>
                    </div>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Purpose</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $borrowRequest->purpose }}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Borrow Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $borrowRequest->borrow_date ? $borrowRequest->borrow_date->format('F d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Return Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $borrowRequest->return_date ? $borrowRequest->return_date->format('F d, Y') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Equipment Items -->
        <x-card title="Equipment Items">
            <div class="space-y-4">
                @foreach($borrowRequest->items as $item)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $item->equipment->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Requested: {{ $item->quantity }} | Returned: {{ $item->returned_quantity }}
                                </p>
                            </div>
                            @if($item->condition)
                                <x-status-badge :status="$item->condition" />
                            @endif
                        </div>
                        @if($item->damage_notes)
                            <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                                Damage: {{ $item->damage_notes }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-card>

        <!-- Timeline -->
        <x-card title="Timeline">
            @php
                $timelineItems = collect([
                    [
                        'title' => 'Request Created',
                        'time' => $borrowRequest->created_at->format('M d, Y H:i'),
                        'icon' => 'document',
                        'description' => 'Created by ' . $borrowRequest->user->name
                    ],
                    $borrowRequest->approved_at ? [
                        'title' => 'Approved',
                        'time' => $borrowRequest->approved_at->format('M d, Y H:i'),
                        'icon' => 'check-circle',
                        'description' => 'Approved by ' . $borrowRequest->approvedBy->name
                    ] : null,
                    $borrowRequest->rejected_at ? [
                        'title' => 'Rejected',
                        'time' => $borrowRequest->rejected_at->format('M d, Y H:i'),
                        'icon' => 'x-circle',
                        'description' => $borrowRequest->rejection_reason
                    ] : null,
                    $borrowRequest->checked_out_at ? [
                        'title' => 'Checked Out',
                        'time' => $borrowRequest->checked_out_at->format('M d, Y H:i'),
                        'icon' => 'arrow-right-on-rectangle',
                        'description' => 'Checked out by ' . $borrowRequest->checkedOutBy->name
                    ] : null,
                    $borrowRequest->returned_at ? [
                        'title' => 'Returned',
                        'time' => $borrowRequest->returned_at->format('M d, Y H:i'),
                        'icon' => 'arrow-left-on-rectangle',
                        'description' => 'Returned by ' . $borrowRequest->returnedBy->name
                    ] : null,
                    $borrowRequest->cancelled_at ? [
                        'title' => 'Cancelled',
                        'time' => $borrowRequest->cancelled_at->format('M d, Y H:i'),
                        'icon' => 'x-mark',
                        'description' => 'Cancelled by ' . $borrowRequest->cancelledBy->name
                    ] : null,
                ])->filter()->values()->toArray();
            @endphp
            <x-timeline :items="$timelineItems" />
        </x-card>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Requester Info -->
        <x-card title="Requester">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                    {{ strtoupper(substr($borrowRequest->user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $borrowRequest->user->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $borrowRequest->user->email }}</p>
                </div>
            </div>
            @if($borrowRequest->organization)
                <div class="mt-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Organization</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $borrowRequest->organization->name }}</p>
                </div>
            @endif
        </x-card>

        <!-- QR Code -->
        @if($borrowRequest->qr_code)
            <x-card title="QR Code">
                <div class="flex justify-center">
                    <img src="{{ asset($borrowRequest->qr_code) }}" alt="Borrow Request QR Code" class="w-48 h-48" />
                </div>
                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">
                    Scan this QR code for check-out/check-in
                </p>
            </x-card>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<x-modal name="reject-modal" :show="false">
    <form action="{{ route('equipment.borrow-requests.reject', $borrowRequest) }}" method="POST">
        @csrf
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reject Borrow Request</h3>
            <x-textarea
                name="reason"
                label="Reason for Rejection"
                placeholder="Please provide a reason for rejecting this request..."
                rows="4"
                required
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

<!-- Return Modal -->
<x-modal name="return-modal" :show="false">
    <form action="{{ route('equipment.borrow-requests.return', $borrowRequest) }}" method="POST">
        @csrf
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Return Equipment</h3>
            <div class="space-y-4">
                @foreach($borrowRequest->items as $index => $item)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="font-medium text-gray-900 dark:text-white mb-2">{{ $item->equipment->name }}</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Returned Quantity</label>
                                <input type="number" name="items[{{ $index }}][equipment_id]" value="{{ $item->equipment_id }}" class="hidden">
                                <input type="number" name="items[{{ $index }}][returned_quantity]" min="0" max="{{ $item->quantity }}" value="{{ $item->quantity }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition</label>
                                <select name="items[{{ $index }}][condition]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="good">Good</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="lost">Lost</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Damage Notes (if applicable)</label>
                            <input type="text" name="items[{{ $index }}][damage_notes]" placeholder="Describe any damage..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
            <button type="button" x-on:click="$dispatch('close-modal', 'return-modal')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Return Equipment
            </button>
        </div>
    </form>
</x-modal>
</x-app-layout>
