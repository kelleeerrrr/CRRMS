<x-app-layout>
    @php($editing = isset($equipment))
    <x-slot name="title">{{ $editing ? 'Edit Equipment' : 'Add Equipment' }}</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit Equipment' : 'Add Equipment' }}</h1>
        <p class="text-gray-600 dark:text-gray-400">{{ $editing ? 'Update equipment details' : 'Create a new equipment item' }}</p>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ $editing ? route('equipment.update', $equipment) : route('equipment.store') }}" enctype="multipart/form-data">
            @csrf
            @if($editing)
                @method('PUT')
            @endif

            <x-input-error :messages="$errors->all()" class="mb-4" />
            
            <!-- Basic Information -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="text"
                    name="name"
                    label="Equipment Name *"
                    required
                    :value="old('name', $equipment->name ?? null)"
                    :error="$errors->first('name')"
                />

                <x-select
                    name="equipment_category"
                    label="Equipment Category *"
                    required
                    :options="[
                        '' => 'Select Category',
                        'Computer' => 'Computer',
                        'Projector' => 'Projector',
                        'Audio Equipment' => 'Audio Equipment',
                        'Laboratory Equipment' => 'Laboratory Equipment',
                        'Sports Equipment' => 'Sports Equipment',
                        'Office Equipment' => 'Office Equipment',
                        'Other' => 'Other'
                    ]"
                    :value="old('equipment_category', $equipment->equipment_category ?? ($editing ? 'Other' : null))"
                    :error="$errors->first('equipment_category')"
                />

                <x-input
                    type="textarea"
                    name="description"
                    label="Description"
                    rows="2"
                    :value="old('description', $equipment->description ?? null)"
                />

                <x-input
                    type="text"
                    name="room_storage"
                    label="Building or Facility"
                    :value="old('room_storage', $equipment->room_storage ?? null)"
                />
            </div>

            <!-- Quantity and Condition -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Quantity and Condition</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="number"
                    name="total_quantity"
                    label="Total Quantity *"
                    required
                    min="1"
                    :value="old('total_quantity', $equipment->total_quantity ?? null) ?? 1"
                    :error="$errors->first('total_quantity')"
                />

                <x-select
                    name="condition"
                    label="Condition *"
                    required
                    :options="[
                        'New' => 'New',
                        'Good' => 'Good',
                        'Fair' => 'Fair',
                        'Poor' => 'Poor',
                        'Damaged' => 'Damaged'
                    ]"
                    :value="old('condition', isset($equipment) ? ucfirst($equipment->condition === 'excellent' ? 'new' : $equipment->condition) : null) ?? 'Good'"
                    :error="$errors->first('condition')"
                />

                <div>
                    <x-image-picker name="equipment_photo" :label="empty($equipment->equipment_photo) ? 'Equipment photo' : 'Replace equipment photo'" :saved="empty($equipment->equipment_photo) ? [] : [$equipment->equipment_photo]" />
                </div>
            </div>

            <!-- Management Information -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Management Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="text"
                    name="responsible_office"
                    label="Responsible Office / Custodian"
                    :value="old('responsible_office', $equipment->responsible_office ?? null)"
                />

                <x-input
                    type="text"
                    name="contact_number"
                    label="Contact Number"
                    :value="old('contact_number', $equipment->contact_number ?? null)"
                />

                <x-input
                    type="textarea"
                    name="additional_notes"
                    label="Additional Notes"
                    rows="2"
                    :value="old('additional_notes', $equipment->additional_notes ?? null)"
                />
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('equipment.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    {{ $editing ? 'Save Changes' : 'Create Equipment' }}
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
