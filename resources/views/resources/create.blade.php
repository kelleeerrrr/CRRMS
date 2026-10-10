<x-app-layout>
    <x-slot name="title">Add Resource</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Resource</h1>
        <p class="text-gray-600 dark:text-gray-400">Create a new resource listing</p>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('resources.store') }}" enctype="multipart/form-data">
            @csrf
            
            <!-- 1. Basic Information -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">1. Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="text"
                    name="name"
                    label="Resource Name*"
                    required
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
                />
            </div>

            <div class="mb-6">
                <x-textarea
                    name="description"
                    label="Description*"
                    required
                    rows="3"
                    placeholder="Provide a detailed description of the resource..."
                />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Resource Photo</label>
                <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300">
            </div>

            <!-- 2. Quantity and Condition -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 mt-8">2. Quantity and Condition</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="number"
                    name="total_quantity"
                    label="Total Quantity*"
                    required
                    min="1"
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
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-select
                    name="status"
                    label="Availability Status*"
                    required
                    :options="[
                        'available' => 'Available',
                        'unavailable' => 'Unavailable'
                    ]"
                />

                <x-input
                    type="number"
                    name="available_quantity"
                    label="Available Quantity"
                    placeholder="Calculated automatically"
                    readonly
                />
            </div>

            <!-- 3. Lending Settings -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 mt-8">3. Lending Settings</h2>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Available for Borrowing*</label>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="available_for_borrowing" value="1" required class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Yes</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="available_for_borrowing" value="0" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">No</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="number"
                    name="max_borrowing_duration"
                    label="Maximum Borrowing Duration (days)"
                    placeholder="e.g., 7"
                    min="1"
                />

                <x-input
                    type="text"
                    name="pickup_location"
                    label="Preferred Pickup or Handover Location"
                    placeholder="e.g., Library, Room 101"
                />
            </div>

            <div class="mb-6">
                <x-textarea
                    name="borrowing_restrictions"
                    label="Borrowing Restrictions / Instructions"
                    rows="3"
                    placeholder="Any restrictions or special instructions for borrowing..."
                />
            </div>

            <!-- 4. Contact and Additional Information -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 mt-8">4. Contact and Additional Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <x-input
                    type="text"
                    name="contact_number"
                    label="Contact Number"
                    :value="auth()->user()->phone_number ?? ''"
                    placeholder="Auto-filled from profile"
                />

                <div></div>
            </div>

            <div class="mb-6">
                <x-textarea
                    name="additional_notes"
                    label="Additional Notes"
                    rows="3"
                    placeholder="Any additional information..."
                />
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('resources.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Resource
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
