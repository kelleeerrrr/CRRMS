<x-app-layout>
    <x-slot name="title">Add Equipment</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Equipment</h1>
        <p class="text-gray-600 dark:text-gray-400">Create a new equipment item</p>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('equipment.store') }}">
            @csrf
            
            <!-- Basic Information -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="text"
                    name="name"
                    label="Equipment Name *"
                    required
                    :value="old('name')"
                    :error="$errors->first('name')"
                />

                <x-input
                    type="text"
                    name="equipment_code"
                    label="Equipment Code / Asset Tag *"
                    required
                    :value="old('equipment_code')"
                    :error="$errors->first('equipment_code')"
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
                    :value="old('equipment_category')"
                    :error="$errors->first('equipment_category')"
                />

                <x-input
                    type="textarea"
                    name="description"
                    label="Description"
                    rows="2"
                    :value="old('description')"
                />
            </div>

            <!-- Location -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Location</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="text"
                    name="location_building"
                    label="Building / Location *"
                    required
                    :value="old('location_building')"
                    :error="$errors->first('location_building')"
                />

                <x-input
                    type="text"
                    name="room_storage"
                    label="Room / Storage Area"
                    :value="old('room_storage')"
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
                    :value="old('total_quantity') ?? 1"
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
                    :value="old('condition') ?? 'Good'"
                    :error="$errors->first('condition')"
                />

                <x-select
                    name="availability_status"
                    label="Availability Status *"
                    required
                    :options="[
                        'Available' => 'Available',
                        'Unavailable' => 'Unavailable',
                        'Under Maintenance' => 'Under Maintenance',
                        'Retired' => 'Retired'
                    ]"
                    :value="old('availability_status') ?? 'Available'"
                    :error="$errors->first('availability_status')"
                />

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Equipment Photo</label>
                    <input type="file" name="equipment_photo" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300">
                </div>
            </div>

            <!-- Borrowing Settings -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Borrowing Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Available for Borrowing *</label>
                    <select name="available_for_borrowing" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="1" {{ old('available_for_borrowing', '1') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('available_for_borrowing', '1') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <x-input
                    type="number"
                    name="max_borrowing_duration"
                    label="Maximum Borrowing Duration (days)"
                    min="1"
                    :value="old('max_borrowing_duration')"
                />

                <x-input
                    type="number"
                    name="advance_booking_requirement"
                    label="Advance Booking Requirement (days)"
                    min="0"
                    :value="old('advance_booking_requirement')"
                />

                <x-input
                    type="textarea"
                    name="borrowing_restrictions"
                    label="Borrowing Restrictions / Instructions"
                    rows="2"
                    :value="old('borrowing_restrictions')"
                />
            </div>

            <!-- Management Information -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 border-b pb-2">Management Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="text"
                    name="responsible_office"
                    label="Responsible Office / Custodian"
                    :value="old('responsible_office')"
                />

                <x-input
                    type="textarea"
                    name="additional_notes"
                    label="Additional Notes"
                    rows="2"
                    :value="old('additional_notes')"
                />
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <a href="{{ route('equipment.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Equipment
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
