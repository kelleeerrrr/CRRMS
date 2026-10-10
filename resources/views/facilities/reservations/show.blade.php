<x-app-layout>
<x-slot name="title">Reservation Details</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $reservation->title }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ $reservation->facility->name }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('my-lending.requests') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Back to My Requests
            </a>
            @if(auth()->user()->can('update', $reservation) && $reservation->status === 'pending')
                <a href="{{ route('facilities.reservations.edit', $reservation) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Edit
                </a>
            @endif
            @if(auth()->user()->can('approve', $reservation) && $reservation->status === 'pending')
                <form action="{{ route('facilities.reservations.approve', $reservation) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Approve
                    </button>
                </form>
                <button @click="openRejectModal = true" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Reject
                </button>
            @endif
            @if(auth()->user()->can('delete', $reservation) && in_array($reservation->status, ['pending', 'approved']))
                <form action="{{ route('facilities.reservations.destroy', $reservation) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this reservation?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Cancel
                    </button>
                </form>
            @endif
            @if(auth()->user()->can('checkIn', $reservation) && $reservation->status === 'approved' && !$reservation->checked_in_at)
                <form action="{{ route('facilities.reservations.check-in', $reservation) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Check In
                    </button>
                </form>
            @endif
            @if(auth()->user()->can('checkOut', $reservation) && $reservation->checked_in_at && !$reservation->checked_out_at)
                <form action="{{ route('facilities.reservations.check-out', $reservation) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Check Out
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Reservation Details -->
        <x-card title="Reservation Details">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        <x-status-badge :status="$reservation->status" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Reservation ID</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reservation->id }}</p>
                    </div>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Facility</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reservation->facility->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $reservation->facility->room->name ?? 'N/A' }} - {{ $reservation->facility->building->name ?? 'N/A' }}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Start Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reservation->start_date->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">End Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reservation->end_date->format('F d, Y') }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Start Time</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reservation->start_time->format('H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">End Time</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reservation->end_time->format('H:i') }}</p>
                    </div>
                </div>
                
                @if($reservation->purpose)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Purpose</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reservation->purpose }}</p>
                    </div>
                @endif
                
                @if($reservation->description)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $reservation->description }}</p>
                    </div>
                @endif
                
                @if($reservation->attendees_count)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Expected Attendees</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reservation->attendees_count }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Timeline -->
        <x-card title="Timeline">
            @php
                $timelineItems = [
                    [
                        'title' => 'Reservation Created',
                        'time' => $reservation->created_at->format('M d, Y H:i'),
                        'icon' => 'calendar',
                        'description' => 'Created by ' . $reservation->user->name
                    ],
                    $reservation->approved_at ? [
                        'title' => 'Approved',
                        'time' => $reservation->approved_at->format('M d, Y H:i'),
                        'icon' => 'check-circle',
                        'description' => 'Approved by ' . $reservation->approvedBy->name
                    ] : null,
                    $reservation->rejected_at ? [
                        'title' => 'Rejected',
                        'time' => $reservation->rejected_at->format('M d, Y H:i'),
                        'icon' => 'x-circle',
                        'description' => $reservation->rejection_reason
                    ] : null,
                    $reservation->checked_in_at ? [
                        'title' => 'Checked In',
                        'time' => $reservation->checked_in_at->format('M d, Y H:i'),
                        'icon' => 'arrow-right-on-rectangle',
                        'description' => 'Checked in by ' . $reservation->checkedInBy->name
                    ] : null,
                    $reservation->checked_out_at ? [
                        'title' => 'Checked Out',
                        'time' => $reservation->checked_out_at->format('M d, Y H:i'),
                        'icon' => 'arrow-left-on-rectangle',
                        'description' => 'Checked out by ' . $reservation->checkedOutBy->name
                    ] : null,
                    $reservation->cancelled_at ? [
                        'title' => 'Cancelled',
                        'time' => $reservation->cancelled_at->format('M d, Y H:i'),
                        'icon' => 'x-mark',
                        'description' => 'Cancelled by ' . $reservation->cancelledBy->name
                    ] : null,
                ];
                $timelineItems = array_filter($timelineItems);
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
                    {{ strtoupper(substr($reservation->user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $reservation->user->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $reservation->user->email }}</p>
                </div>
            </div>
            @if($reservation->organization)
                <div class="mt-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Organization</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reservation->organization->name }}</p>
                </div>
            @endif
        </x-card>

        <!-- QR Code -->
        @if($reservation->qr_code)
            <x-card title="QR Code">
                <div class="flex justify-center">
                    <img src="{{ asset($reservation->qr_code) }}" alt="Reservation QR Code" class="w-48 h-48" />
                </div>
                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">
                    Scan this QR code for check-in/check-out
                </p>
            </x-card>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<x-modal name="reject-modal" :show="false">
    <form action="{{ route('facilities.reservations.reject', $reservation) }}" method="POST">
        @csrf
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reject Reservation</h3>
            <x-textarea
                name="reason"
                label="Reason for Rejection"
                placeholder="Please provide a reason for rejecting this reservation..."
                rows="4"
                required
            />
        </div>
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
            <button type="button" @click="$dispatch('close-modal', 'reject-modal')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Reject Reservation
            </button>
        </div>
    </form>
</x-modal>
</x-app-layout>
