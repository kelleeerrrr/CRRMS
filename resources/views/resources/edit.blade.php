<x-app-layout>
    <x-slot name="title">Edit Resource</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Resource</h1>
        <p class="text-gray-600 dark:text-gray-400">Update your resource details</p>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('resources.update', $resource) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- 1. Basic Information -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">1. Basic Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="text"
                    name="name"
                    label="Resource Name*"
                    required
                    :value="$resource->name"
                />

                <x-select
                    name="category"
                    label="Resource Category*"
                    required
                    :options="[
                        '' => 'Select Category',
                        'Books' => 'Books',
                        'Electronics' => 'Electronics',
                        'Tools' => 'Tools',
                        'Sports Equipment' => 'Sports Equipment',
                        'School Supplies' => 'School Supplies',
                        'Other' => 'Other'
                    ]"
                    :value="$resource->category"
                />
            </div>

            <div class="mb-6">
                <x-textarea
                    name="description"
                    label="Description"
                    rows="3"
                    placeholder="Provide a detailed description of the resource..."
                    :value="$resource->description"
                />
            </div>

            <div class="mb-6">
                <x-image-picker name="photo" label="Resource photo" :saved="$resource->photo ? [$resource->photo] : []" />
            </div>

            <!-- 2. Quantity and Condition -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 mt-8">2. Quantity and Condition</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="number"
                    name="available_quantity"
                    label="Available Quantity*"
                    required
                    min="1"
                    :value="$resource->available_quantity"
                />

                <x-select
                    name="condition"
                    label="Condition*"
                    required
                    :options="[
                        '' => 'Select Condition',
                        'New' => 'New',
                        'Good' => 'Good',
                        'Fair' => 'Fair',
                        'Poor' => 'Poor',
                        'Damaged' => 'Damaged'
                    ]"
                    :value="ucfirst($resource->condition)"
                />
            </div>

            <!-- 3. Lending Settings -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 mt-8">3. Lending Settings</h2>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">For Borrowing?*</label>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="available_for_borrowing" value="1" @checked($resource->available_for_borrowing) required class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" onchange="toggleLendingFields(this.value)">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="available_for_borrowing" value="0" @checked(!$resource->available_for_borrowing) class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" onchange="toggleLendingFields(this.value)">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                    </label>
                </div>
            </div>

            <div id="lending-fields" class="@if(!$resource->available_for_borrowing) hidden @endif">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <x-input
                        type="number"
                        name="max_borrowing_duration"
                        label="Maximum Borrowing Duration (days)"
                        placeholder="e.g., 7"
                        min="1"
                        :value="$resource->max_borrowing_duration"
                    />

                    <x-input
                        type="text"
                        name="pickup_location"
                        label="Preferred Pickup or Handover Location"
                        placeholder="e.g., Library, Room 101"
                        :value="$resource->pickup_location"
                    />
                </div>

                <div class="mb-6">
                    <x-textarea
                        name="borrowing_restrictions"
                        label="Borrowing Restrictions / Instructions"
                        rows="3"
                        placeholder="Any restrictions or special instructions for borrowing..."
                        :value="$resource->borrowing_restrictions"
                    />
                </div>
            </div>

            <!-- 4. Contact and Additional Information -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 mt-8">4. Contact and Additional Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="text"
                    name="contact_name"
                    label="Name"
                    :value="auth()->user()->name"
                    readonly
                />

                <x-input
                    type="text"
                    name="contact_number"
                    label="Contact Number"
                    :value="$resource->contact_number ?? auth()->user()->phone_number ?? ''"
                    placeholder="Auto-filled from profile"
                />
            </div>

            <div class="mb-6">
                <x-textarea
                    name="additional_notes"
                    label="Additional Notes"
                    rows="3"
                    placeholder="Any additional information..."
                    :value="$resource->additional_notes"
                />
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('resources.show', $resource) }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update Resource
                </button>
            </div>
        </form>
    </x-card>

    <script>
        function toggleLendingFields(value) {
            const lendingFields = document.getElementById('lending-fields');
            if (value === '1') {
                lendingFields.classList.remove('hidden');
            } else {
                lendingFields.classList.add('hidden');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const selectedValue = document.querySelector('input[name="available_for_borrowing"]:checked')?.value;
            if (selectedValue) {
                toggleLendingFields(selectedValue);
            }
        });
    </script>
</x-app-layout>
