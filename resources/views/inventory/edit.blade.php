<x-slot name="title">Edit Inventory</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Inventory', 'url' => route('inventory.index')],
        ['title' => 'Edit', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Inventory</h1>
    <p class="text-gray-600 dark:text-gray-400">Update inventory record</p>
</x-slot>

<x-card>
    <form action="{{ route('inventory.update', $inventory) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-select
                name="equipment_id"
                label="Equipment"
                :options="['' => 'Select equipment'] + $equipment->pluck('name', 'id')->toArray()"
                required
                :value="old('equipment_id', $inventory->equipment_id)"
                :error="$errors->first('equipment_id')"
            />
            
            <x-input
                type="number"
                name="quantity"
                label="Quantity"
                placeholder="0"
                min="0"
                required
                :value="old('quantity', $inventory->quantity)"
                :error="$errors->first('quantity')"
            />
            
            <x-input
                type="text"
                name="location"
                label="Location"
                placeholder="e.g., Room 101, Warehouse A"
                required
                :value="old('location', $inventory->location)"
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
                :value="old('condition', $inventory->condition)"
            />
        </div>
        
        <div class="mt-6">
            <x-textarea
                name="notes"
                label="Notes"
                placeholder="Additional notes about this inventory..."
                rows="4"
                :value="old('notes', $inventory->notes)"
                :error="$errors->first('notes')"
            />
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('inventory.show', $inventory) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Inventory
            </button>
        </div>
    </form>
</x-card>
