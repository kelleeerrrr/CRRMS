<x-slot name="title">Add Inventory</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Inventory', 'url' => route('inventory.index')],
        ['title' => 'Add', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Inventory</h1>
    <p class="text-gray-600 dark:text-gray-400">Add new inventory record</p>
</x-slot>

<x-card>
    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-select
                name="equipment_id"
                label="Equipment"
                :options="['' => 'Select equipment'] + $equipment->pluck('name', 'id')->toArray()"
                required
                :value="old('equipment_id')"
                :error="$errors->first('equipment_id')"
            />
            
            <x-input
                type="number"
                name="quantity"
                label="Quantity"
                placeholder="0"
                min="0"
                required
                :value="old('quantity')"
                :error="$errors->first('quantity')"
            />
            
            <x-input
                type="text"
                name="location"
                label="Location"
                placeholder="e.g., Room 101, Warehouse A"
                required
                :value="old('location')"
                :error="$errors->first('location')"
            />
            
            <x-select
                name="condition"
                label="Condition"
                :options="[
                    '' => 'Select condition',
                    'good' => 'Good',
                    'fair' => 'Fair',
                    'poor' => 'Poor',
                    'damaged' => 'Damaged'
                ]"
                :value="old('condition')"
            />
        </div>
        
        <div class="mt-6">
            <x-textarea
                name="notes"
                label="Notes"
                placeholder="Additional notes about this inventory..."
                rows="4"
                :value="old('notes')"
                :error="$errors->first('notes')"
            />
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Add Inventory
            </button>
        </div>
    </form>
</x-card>
