<x-slot name="title">{{ $maintenanceLog->title }}</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Maintenance', 'url' => route('maintenance.index')],
        ['title' => $maintenanceLog->title, 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $maintenanceLog->title }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ $maintenanceLog->type === 'equipment' ? 'Equipment' : 'Facility' }} Maintenance</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('maintenance.edit', $maintenanceLog) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Edit
            </a>
            <a href="{{ route('maintenance.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Back
            </a>
        </div>
    </div>
</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Maintenance Details -->
        <x-card title="Maintenance Details">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Title</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $maintenanceLog->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($maintenanceLog->type) }}</p>
                    </div>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $maintenanceLog->description }}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Priority</p>
                        @if($maintenanceLog->priority === 'urgent')
                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Urgent</span>
                        @elseif($maintenanceLog->priority === 'high')
                            <span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full dark:bg-orange-900 dark:text-orange-100">High</span>
                        @elseif($maintenanceLog->priority === 'medium')
                            <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-100">Medium</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">Low</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        @if($maintenanceLog->status === 'pending')
                            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">Pending</span>
                        @elseif($maintenanceLog->status === 'in_progress')
                            <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-100">In Progress</span>
                        @elseif($maintenanceLog->status === 'completed')
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-100">Completed</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Cancelled</span>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Assigned To</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $maintenanceLog->assignedTo?->name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Scheduled Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $maintenanceLog->scheduled_date?->format('F j, Y') ?? 'Not scheduled' }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created By</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $maintenanceLog->createdBy->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created At</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $maintenanceLog->created_at->format('F j, Y, g:i A') }}</p>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Related Item -->
        <x-card title="{{ ucfirst($maintenanceLog->type) }} Information">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold mr-4">
                    {{ strtoupper(substr($maintenanceLog->maintainable->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $maintenanceLog->maintainable->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $maintenanceLog->maintainable->location ?? 'No location' }}</p>
                </div>
            </div>
        </x-card>

        <!-- Completion Details -->
        @if($maintenanceLog->status === 'completed')
            <x-card title="Completion Details">
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Completed At</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $maintenanceLog->completed_at->format('F j, Y, g:i A') }}</p>
                    </div>
                    @if($maintenanceLog->completion_notes)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Completion Notes</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $maintenanceLog->completion_notes }}</p>
                        </div>
                    @endif
                    @if($maintenanceLog->cost)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Cost</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($maintenanceLog->cost, 2) }}</p>
                        </div>
                    @endif
                </div>
            </x-card>
        @endif

        @if($maintenanceLog->status === 'cancelled')
            <x-card title="Cancellation Details">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Reason</p>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $maintenanceLog->cancellation_reason }}</p>
                </div>
            </x-card>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Actions -->
        <x-card title="Actions">
            <div class="space-y-2">
                @if($maintenanceLog->status === 'pending' || $maintenanceLog->status === 'in_progress')
                    <form action="{{ route('maintenance.complete', $maintenanceLog) }}" method="POST" class="inline w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <x-heroicon-o-check class="w-5 h-5 mr-2" />
                            Mark as Complete
                        </button>
                    </form>
                @endif
                
                @if($maintenanceLog->status === 'pending' || $maintenanceLog->status === 'in_progress')
                    <button onclick="document.getElementById('cancel-modal').classList.remove('hidden')" class="w-full flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <x-heroicon-o-x-mark class="w-5 h-5 mr-2" />
                        Cancel Maintenance
                    </button>
                @endif
                
                @if($maintenanceLog->status === 'pending' && !$maintenanceLog->assigned_to)
                    <button onclick="document.getElementById('assign-modal').classList.remove('hidden')" class="w-full flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <x-heroicon-o-user-plus class="w-5 h-5 mr-2" />
                        Assign Technician
                    </button>
                @endif
            </div>
        </x-card>

        <!-- Quick Actions -->
        <x-card title="Quick Actions">
            <div class="space-y-2">
                <a href="{{ route('maintenance.index') }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-3" />
                    Back to Maintenance
                </a>
                <form action="{{ route('maintenance.destroy', $maintenanceLog) }}" method="POST" class="inline w-full" onsubmit="return confirm('Are you sure you want to delete this maintenance log?')">
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
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cancel Maintenance</h3>
        <form action="{{ route('maintenance.cancel', $maintenanceLog) }}" method="POST">
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

<!-- Assign Technician Modal -->
<div id="assign-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assign Technician</h3>
        <form action="{{ route('maintenance.assign-technician', $maintenanceLog) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Technician</label>
                <select name="technician_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select technician</option>
                    @php
                        $technicians = \App\Models\User::whereHas('roles', function($q) { $q->where('name', 'technician'); })->get();
                    @endphp
                    @foreach($technicians as $technician)
                        <option value="{{ $technician->id }}">{{ $technician->name }}</option>
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
