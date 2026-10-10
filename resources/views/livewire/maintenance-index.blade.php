<div>
    <!-- Filters -->
    <x-card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <x-input
                type="text"
                wire:model.live="search"
                placeholder="Search maintenance logs..."
                label="Search"
            />
            
            <x-select
                wire:model.live="type"
                label="Type"
                :options="[
                    '' => 'All Types',
                    'equipment' => 'Equipment',
                    'facility' => 'Facility'
                ]"
            />
            
            <x-select
                wire:model.live="status"
                label="Status"
                :options="[
                    '' => 'All Status',
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled'
                ]"
            />
            
            <x-select
                wire:model.live="priority"
                label="Priority"
                :options="[
                    '' => 'All Priorities',
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'urgent' => 'Urgent'
                ]"
            />
            
            <x-select
                wire:model.live="assigned_to"
                label="Assigned To"
                :options="['' => 'All Technicians'] + $technicians->pluck('name', 'id')->toArray()"
            />
        </div>
        
        <div class="mt-4 flex justify-end">
            <x-button variant="ghost" wire:click="resetFilters">
                Reset Filters
            </x-button>
        </div>
    </x-card>

    <!-- Table -->
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Maintenance Logs ({{ $maintenanceLogs->total() }})
            </h3>
            <a href="{{ route('maintenance.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                Add Maintenance Log
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('title')">
                            Title
                            @if($sortField === 'title')
                                <x-heroicon-o-chevron-up class="w-4 h-4 inline" x-show="{{ $sortDirection === 'asc' }}" />
                                <x-heroicon-o-chevron-down class="w-4 h-4 inline" x-show="{{ $sortDirection === 'desc' }}" />
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Priority
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Assigned To
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Scheduled Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($maintenanceLogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->title }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $log->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold {{ $log->type === 'equipment' ? 'text-blue-800 bg-blue-100 dark:bg-blue-900 dark:text-blue-100' : 'text-purple-800 bg-purple-100 dark:bg-purple-900 dark:text-purple-100' }} rounded-full">
                                    {{ ucfirst($log->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->priority === 'urgent')
                                    <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Urgent</span>
                                @elseif($log->priority === 'high')
                                    <span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full dark:bg-orange-900 dark:text-orange-100">High</span>
                                @elseif($log->priority === 'medium')
                                    <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-100">Medium</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">Low</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">Pending</span>
                                @elseif($log->status === 'in_progress')
                                    <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-100">In Progress</span>
                                @elseif($log->status === 'completed')
                                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-100">Completed</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Cancelled</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $log->assignedTo?->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $log->scheduled_date?->format('M j, Y') ?? 'Not scheduled' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('maintenance.show', $log) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                    View
                                </a>
                                <a href="{{ route('maintenance.edit', $log) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <x-empty-state title="No maintenance logs found" description="Create a maintenance log to get started." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($maintenanceLogs->hasPages())
            <div class="mt-6">
                {{ $maintenanceLogs->links() }}
            </div>
        @endif
    </x-card>
</div>
