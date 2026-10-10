<x-slot name="title">Edit Building</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Buildings', 'url' => route('buildings.index')],
        ['title' => 'Edit', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Building</h1>
    <p class="text-gray-600 dark:text-gray-400">Update building information</p>
</x-slot>

<x-card>
    <form action="{{ route('buildings.update', $building) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input
                type="text"
                name="name"
                label="Building Name"
                placeholder="e.g., Main Building, Science Center"
                required
                :value="old('name', $building->name)"
                :error="$errors->first('name')"
            />
            
            <x-input
                type="text"
                name="code"
                label="Building Code"
                placeholder="e.g., MB, SC"
                required
                :value="old('code', $building->code)"
                :error="$errors->first('code')"
            />
            
            <x-input
                type="text"
                name="address"
                label="Address"
                placeholder="e.g., 123 Campus Drive"
                :value="old('address', $building->address)"
                :error="$errors->first('address')"
            />
            
            <x-input
                type="number"
                name="total_floors"
                label="Total Floors"
                placeholder="0"
                min="0"
                :value="old('total_floors', $building->total_floors)"
                :error="$errors->first('total_floors')"
            />
        </div>
        
        <div class="mt-6">
            <x-textarea
                name="description"
                label="Description"
                placeholder="Additional details about this building..."
                rows="4"
                :value="old('description', $building->description)"
                :error="$errors->first('description')"
            />
        </div>
        
        <div class="mt-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $building->is_active) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
            </label>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('buildings.show', $building) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Building
            </button>
        </div>
    </form>
</x-card>
