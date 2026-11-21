@php
    $isRequester = $request->user_id === auth()->id();
    $fromApproved = request()->query('from') === 'approved';
@endphp

<x-app-layout>
    <x-slot name="title">Resource Borrow Request Details</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $request->purpose }}</h1>
                <p class="text-gray-600 dark:text-gray-400">Request ID: {{ $request->id }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ $isRequester ? route($fromApproved ? 'borrower.approved' : 'borrower.requests', ['category' => request()->query('category', 'resources'), 'search' => request()->query('search')]) : route('resource-requests.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    {{ $isRequester ? ($fromApproved ? 'Back to Approved' : 'Back to Requests') : 'Back to Resource Requests' }}
                </a>
                @if($request->resource->owner && $request->resource->owner->id !== auth()->id())
                    <form action="{{ route('chat.with-owner') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="resource_type" value="resource">
                        <input type="hidden" name="resource_id" value="{{ $request->resource->id }}">
                        <input type="hidden" name="transaction_id" value="{{ $request->id }}">
                        <input type="hidden" name="transaction_type" value="resource_borrow_request">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Chat with Owner
                        </button>
                    </form>
                @endif
                @if($request->canBeApprovedBy(auth()->user()) && in_array($request->status, ['approved', 'in_use'], true))
                    <form action="{{ route($request->status === 'approved' ? 'resource-requests.check-out' : 'resource-requests.return', $request) }}" method="POST">
                        @csrf
                        <x-button type="submit" variant="success">{{ $request->status === 'approved' ? 'Release Resource' : 'Record Return' }}</x-button>
                    </form>
                @endif
                @if($isRequester && $request->status === 'pending')
                    <a href="{{ route('resource-borrow-requests.edit', $request) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Edit
                    </a>
                @endif
                @if(!$isRequester && $request->canBeApprovedBy(auth()->user()) && $request->status === 'pending')
                    <form action="{{ route('resource-requests.approve', $request) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Approve
                        </button>
                    </form>
                    <form action="{{ route('resource-requests.decline', $request) }}" method="POST" class="inline" onsubmit="return confirm('Reject this pending resource request?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Reject Request
                        </button>
                    </form>
                @endif
                @if($isRequester && $request->status === 'pending')
                    <form action="{{ route('resource-borrow-requests.destroy', $request) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to withdraw your request?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Withdraw Request
                        </button>
                    </form>
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
                        <x-status-badge :status="$request->status" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Request ID</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->id }}</p>
                    </div>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Resource</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->resource->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $request->resource->category ?? 'N/A' }}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Borrow Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->start_date ? $request->start_date->format('F d, Y H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Return Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->end_date ? $request->end_date->format('F d, Y H:i') : 'N/A' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Quantity</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->quantity }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Purpose</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->purpose }}</p>
                    </div>
                </div>

                @if($request->intended_use)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Intended Use</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->intended_use }}</p>
                    </div>
                @endif

                @if($request->notes)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Additional Notes</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $request->notes }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Timeline -->
        <x-card title="Timeline">
            @php
                $timelineItems = [
                    [
                        'title' => 'Request Created',
                        'time' => $request->created_at->format('M d, Y H:i'),
                        'icon' => 'calendar',
                        'description' => 'Created by ' . $request->user->name
                    ],
                ];
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
                    {{ strtoupper(substr($request->user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $request->user->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $request->user->email }}</p>
                </div>
            </div>
            @if($request->contact_number)
                <div class="mt-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Contact Number</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->contact_number }}</p>
                </div>
            @endif
        </x-card>
    </div>
</div>
</x-app-layout>
