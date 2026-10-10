<x-slot name="title">{{ $serviceRequest->title }}</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Service Requests', 'url' => route('service-requests.index')],
        ['title' => $serviceRequest->title, 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $serviceRequest->title }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $serviceRequest->type)) }} Request</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('service-requests.edit', $serviceRequest) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Edit
            </a>
            <a href="{{ route('service-requests.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Back
            </a>
        </div>
    </div>
</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Service Request Details -->
        <x-card title="Service Request Details">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Title</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $serviceRequest->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                        @if($serviceRequest->type === 'cleaning')
                            <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-100">Cleaning</span>
                        @elseif($serviceRequest->type === 'setup')
                            <span class="px-2 py-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-100">Setup</span>
                        @elseif($serviceRequest->type === 'technical_support')
                            <span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full dark:bg-orange-900 dark:text-orange-100">Technical Support</span>
                        @elseif($serviceRequest->type === 'internet')
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-100">Internet</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-100">Electrical</span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $serviceRequest->description }}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Priority</p>
                        @if($serviceRequest->priority === 'urgent')
                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Urgent</span>
                        @elseif($serviceRequest->priority === 'high')
                            <span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full dark:bg-orange-900 dark:text-orange-100">High</span>
                        @elseif($serviceRequest->priority === 'medium')
                            <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-100">Medium</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">Low</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        @if($serviceRequest->status === 'pending')
                            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">Pending</span>
                        @elseif($serviceRequest->status === 'in_progress')
                            <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-100">In Progress</span>
                        @elseif($serviceRequest->status === 'completed')
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-100">Completed</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Cancelled</span>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Location</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $serviceRequest->location ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Scheduled Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $serviceRequest->scheduled_date?->format('F j, Y') ?? 'Not scheduled' }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Requested By</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $serviceRequest->requestedBy->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Assigned To</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $serviceRequest->assignedTo?->name ?? 'Unassigned' }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created At</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $serviceRequest->created_at->format('F j, Y, g:i A') }}</p>
                    </div>
                    @if($serviceRequest->completed_at)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Completed At</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $serviceRequest->completed_at->format('F j, Y, g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>

        <!-- Completion Details -->
        @if($serviceRequest->status === 'completed')
            <x-card title="Completion Details">
                <div class="space-y-4">
                    @if($serviceRequest->completion_notes)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Completion Notes</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $serviceRequest->completion_notes }}</p>
                        </div>
                    @endif
                </div>
            </x-card>
        @endif

        @if($serviceRequest->status === 'cancelled')
            <x-card title="Cancellation Details">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Reason</p>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $serviceRequest->cancellation_reason }}</p>
                </div>
            </x-card>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Actions -->
        <x-card title="Actions">
            <div class="space-y-2">
                @if($serviceRequest->status === 'pending' || $serviceRequest->status === 'in_progress')
                    <form action="{{ route('service-requests.complete', $serviceRequest) }}" method="POST" class="inline w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <x-heroicon-o-check class="w-5 h-5 mr-2" />
                            Mark as Complete
                        </button>
                    </form>
                @endif
                
                @if($serviceRequest->status === 'pending' || $serviceRequest->status === 'in_progress')
                    <button onclick="document.getElementById('cancel-modal').classList.remove('hidden')" class="w-full flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <x-heroicon-o-x-mark class="w-5 h-5 mr-2" />
                        Cancel Request
                    </button>
                @endif
                
                @if($serviceRequest->status === 'pending' && !$serviceRequest->assigned_to)
                    <button onclick="document.getElementById('assign-modal').classList.remove('hidden')" class="w-full flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <x-heroicon-o-user-plus class="w-5 h-5 mr-2" />
                        Assign Staff
                    </button>
                @endif
            </div>
        </x-card>

        <!-- Quick Actions -->
        <x-card title="Quick Actions">
            <div class="space-y-2">
                <a href="{{ route('service-requests.index') }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-3" />
                    Back to Service Requests
                </a>
                <form action="{{ route('service-requests.destroy', $serviceRequest) }}" method="POST" class="inline w-full" onsubmit="return confirm('Are you sure you want to delete this service request?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                        <x-heroicon-o-trash class="w-5 h-5 mr-3" />
                        Delete
                    </button>
                </form>
            </div>
        </x-card>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancel-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cancel Service Request</h3>
        <form action="{{ route('service-requests.cancel', $serviceRequest) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason</label>
                <textarea name="cancellation_reason" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('cancel-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Confirm Cancellation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Staff Modal -->
<div id="assign-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assign Staff</h3>
        <form action="{{ route('service-requests.assign-staff', $serviceRequest) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Staff Member</label>
                <select name="staff_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select staff</option>
                    @php
                        $staff = \App\Models\User::whereHas('roles', function($q) { $q->whereIn('name', ['staff', 'technician', 'facilities_office']); })->get();
                    @endphp
                    @foreach($staff as $staffMember)
                        <option value="{{ $staffMember->id }}">{{ $staffMember->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('assign-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Assign
                </button>
            </div>
        </form>
    </div>
</div>
