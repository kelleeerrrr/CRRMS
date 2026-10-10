<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Resource</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update your resource details</p>
    </x-slot>

    <x-card class="max-w-2xl mx-auto">
        <form method="POST" action="{{ route('resources.update', $resource) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <x-input
                    name="name"
                    type="text"
                    label="Resource Name"
                    placeholder="e.g., Projector, Calculator, Laptop"
                    :value="$resource->name"
                    required
                />

                <x-input
                    name="category"
                    type="text"
                    label="Category"
                    placeholder="e.g., Electronics, Books, Tools"
                    :value="$resource->category"
                />

                <x-textarea
                    name="description"
                    label="Description"
                    placeholder="Describe your resource..."
                    rows="3"
                >{{ $resource->description }}</x-textarea>

                <x-select
                    name="condition"
                    label="Condition"
                    required
                    :options="[
                        'excellent' => 'Excellent',
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                        'damaged' => 'Damaged'
                    ]"
                    :value="$resource->condition"
                />

                <x-select
                    name="status"
                    label="Status"
                    required
                    :options="[
                        'available' => 'Available',
                        'borrowed' => 'Borrowed',
                        'unavailable' => 'Unavailable',
                        'maintenance' => 'Maintenance'
                    ]"
                    :value="$resource->status"
                />

                <x-textarea
                    name="lending_conditions"
                    label="Lending Conditions"
                    placeholder="Any conditions for borrowing this resource..."
                    rows="2"
                >{{ $resource->lending_conditions }}</x-textarea>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-select
                        name="building_id"
                        label="Building (Optional)"
                        :options="['' => 'Select Building'] + \App\Models\Building::pluck('name', 'id')->toArray()"
                        :value="$resource->building_id"
                    />

                    <x-select
                        name="room_id"
                        label="Room (Optional)"
                        :options="['' => 'Select Room'] + \App\Models\Room::pluck('name', 'id')->toArray()"
                        :value="$resource->room_id"
                    />
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('resources.show', $resource) }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update Resource
                    </button>
                </div>
            </div>
        </form>
    </x-card>
</x-app-layout>
