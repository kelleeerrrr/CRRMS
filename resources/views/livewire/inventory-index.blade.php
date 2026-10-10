<div>
    <!-- Filters -->
    <x-card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-input
                type="text"
                wire:model.live="search"
                placeholder="Search inventory..."
                label="Search"
            />
            
            <x-select
                wire:model.live="equipment_id"
                label="Equipment"
                :options="['' => 'All Equipment'] + $equipment->pluck('name', 'id')->toArray()"
            />
            
            <x-input
                type="text"
                wire:model.live="location"
                placeholder="Filter by location..."
                label="Location"
            />
            
            <x-select
                wire:model.live="condition"
                label="Condition"
                :options="[
                    '' => 'All Conditions',
                    'good' => 'Good',
                    'fair' => 'Fair',
                    'poor' => 'Poor',
                    'damaged' => 'Damaged'
                ]"
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
                Inventory Records ({{ $inventory->total() }})
            </h3>
            @if(auth()->user()->can('create', \App\Models\Inventory::class))
                <a href="{{ route('inventory.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                    Add Inventory
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('equipment.name')">
                            Equipment
                            @if($sortField === 'equipment.name')
                                <x-heroicon-o-chevron-up class="w-4 h-4 inline" x-show="{{ $sortDirection === 'asc' }}" />
                                <x-heroicon-o-chevron-down class="w-4 h-4 inline" x-show="{{ $sortDirection === 'desc' }}" />
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer" wire:click="sortBy('quantity')">
                            Quantity
                            @if($sortField === 'quantity')
                                <x-heroicon-o-chevron-up class="w-4 h-4 inline" x-show="{{ $sortDirection === 'asc' }}" />
                                <x-heroicon-o-chevron-down class="w-4 h-4 inline" x-show="{{ $sortDirection === 'desc' }}" />
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Condition
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Last Counted
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($inventory as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $item->equipment->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->equipment->serial_number ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $item->quantity }}
                                </div>
                                @if($item->quantity <= 5)
                                    <span class="text-xs text-red-600 dark:text-red-400">Low Stock</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $item->location }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :status="$item->condition" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $item->last_counted_at ? $item->last_counted_at->format('M d, Y') : 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->countedBy->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('inventory.show', $item) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                    View
                                </a>
                                @if(auth()->user()->can('update', $item))
                                    <a href="{{ route('inventory.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                        Edit
                                    </a>
                                @endif
                                @if(auth()->user()->can('delete', $item))
                                    <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this inventory record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <x-empty-state title="No inventory records found" description="Try adjusting your filters or add a new inventory record." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($inventory->hasPages())
            <div class="mt-6">
                {{ $inventory->links() }}
            </div>
        @endif
    </x-card>
</div>
