<x-slot name="title">Create Transfer</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Inventory', 'url' => route('inventory.index')],
        ['title' => 'Transfers', 'url' => route('inventory.transfers')],
        ['title' => 'Create', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Transfer</h1>
    <p class="text-gray-600 dark:text-gray-400">Transfer inventory to another location</p>
</x-slot>

<x-card>
    <form action="{{ route('inventory.store-transfer', $inventory) }}" method="POST">
        @csrf
        
        <div class="space-y-4">
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <h3 class="font-medium text-gray-900 dark:text-white mb-2">Source Inventory</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Equipment</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->equipment->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Current Location</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->location }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Available Quantity</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->quantity }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Condition</p>
                        <x-status-badge :status="$inventory->condition" />
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input
                    type="text"
                    name="to_location"
                    label="Destination Location"
                    placeholder="e.g., Room 201, Warehouse B"
                    required
                    :value="old('to_location')"
                    :error="$errors->first('to_location')"
                />
                
                <x-input
                    type="number"
                    name="quantity"
                    label="Quantity to Transfer"
                    placeholder="1"
                    min="1"
                    :max="$inventory->quantity"
                    required
                    :value="old('quantity')"
                    :error="$errors->first('quantity')"
                />
                
                <x-input
                    type="date"
                    name="transfer_date"
                    label="Transfer Date (Optional)"
                    :value="old('transfer_date')"
                    :error="$errors->first('transfer_date')"
                />
            </div>
            
            <div>
                <x-textarea
                    name="notes"
                    label="Notes (Optional)"
                    placeholder="Additional notes about this transfer..."
                    rows="4"
                    :value="old('notes')"
                    :error="$errors->first('notes')"
                />
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('inventory.transfers') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Create Transfer
            </button>
        </div>
    </form>
</x-card>
