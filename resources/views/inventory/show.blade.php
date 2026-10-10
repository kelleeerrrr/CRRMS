<x-slot name="title">Inventory Details</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Inventory', 'url' => route('inventory.index')],
        ['title' => 'Details', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $inventory->equipment->name }}</h1>
            <p class="text-gray-600 dark:text-gray-400">Inventory ID: {{ $inventory->id }}</p>
        </div>
        <div class="flex items-center space-x-3">
            @if(auth()->user()->can('update', $inventory))
                <a href="{{ route('inventory.edit', $inventory) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Edit
                </a>
            @endif
            @if(auth()->user()->can('create', \App\Models\InventoryTransfer::class))
                <a href="{{ route('inventory.create-transfer', $inventory) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Transfer
                </a>
            @endif
        </div>
    </div>
</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Inventory Details -->
        <x-card title="Inventory Details">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Equipment</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->equipment->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $inventory->equipment->serial_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Quantity</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->quantity }}</p>
                        @if($inventory->quantity <= 5)
                            <span class="text-xs text-red-600 dark:text-red-400">Low Stock</span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Location</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->location }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Condition</p>
                    <x-status-badge :status="$inventory->condition" />
                </div>
                
                @if($inventory->notes)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $inventory->notes }}</p>
                    </div>
                @endif
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Last Counted</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->last_counted_at ? $inventory->last_counted_at->format('M d, Y H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Counted By</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->countedBy->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Stock Count -->
        @if(auth()->user()->can('update', $inventory))
            <x-card title="Perform Stock Count">
                <form action="{{ route('inventory.stock-count', $inventory) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expected Quantity</label>
                            <input type="text" value="{{ $inventory->quantity }}" disabled class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Actual Quantity</label>
                            <input type="number" name="actual_quantity" min="0" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Submit Stock Count
                        </button>
                    </div>
                </form>
            </x-card>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Equipment Info -->
        <x-card title="Equipment Information">
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->equipment->category->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Quantity</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->equipment->total_quantity }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Available Quantity</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->equipment->available_quantity }}</p>
                </div>
            </div>
        </x-card>

        <!-- Quick Actions -->
        <x-card title="Quick Actions">
            <div class="space-y-2">
                <a href="{{ route('inventory.index') }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-3" />
                    Back to Inventory
                </a>
                @if(auth()->user()->can('viewAny', \App\Models\InventoryTransfer::class))
                    <a href="{{ route('inventory.transfers') }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <x-heroicon-o-arrow-right-left class="w-5 h-5 mr-3" />
                        View Transfers
                    </a>
                @endif
            </div>
        </x-card>
    </div>
</div>
