<x-app-layout>
    <x-slot name="title">Add Facility</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Facility</h1>
        <p class="text-gray-600 dark:text-gray-400">Create a new facility</p>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('facilities.store') }}">
            @csrf
            
            <!-- 1. Basic Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">1. Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input
                        type="text"
                        name="name"
                        label="Facility Name"
                        required
                    />

                    <x-select
                        name="type"
                        label="Facility Type"
                        required
                        :options="[
                            'Classroom' => 'Classroom',
                            'Laboratory' => 'Laboratory',
                            'Auditorium' => 'Auditorium',
                            'Conference Room' => 'Conference Room',
                            'Function Room' => 'Function Room',
                            'Sports Facility' => 'Sports Facility',
                            'Other' => 'Other'
                        ]"
                    />
                </div>

                <div class="mt-6">
                    <x-input
                        type="textarea"
                        name="location_address"
                        label="Location Address"
                        rows="3"
                    />
                </div>
            </div>

            <!-- 3. Capacity & Amenities -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">3. Capacity & Amenities</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input
                        type="number"
                        name="capacity"
                        label="Capacity"
                        required
                        min="1"
                    />
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Amenities / Features</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="amenities[]" value="Projector" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Projector</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="amenities[]" value="Air Conditioning" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Air Conditioning</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="amenities[]" value="Wi-Fi" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Wi-Fi</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="amenities[]" value="Whiteboard" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Whiteboard</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="amenities[]" value="Computers" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Computers</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="amenities[]" value="Sound System" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Sound System</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- 4. Availability -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">4. Availability</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-select
                        name="status"
                        label="Status"
                        required
                        :options="[
                            'available' => 'Available',
                            'unavailable' => 'Unavailable',
                            'maintenance' => 'Under Maintenance'
                        ]"
                    />

                    <x-input
                        type="text"
                        name="operating_days"
                        label="Operating Days"
                        placeholder="e.g., Monday - Friday"
                    />

                    <x-input
                        type="time"
                        name="opening_time"
                        label="Opening Time"
                    />

                    <x-input
                        type="time"
                        name="closing_time"
                        label="Closing Time"
                    />
                </div>
            </div>

            <!-- 5. Reservation Settings -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">5. Reservation Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input
                        type="number"
                        name="max_reservation_duration"
                        label="Maximum Reservation Duration (hours)"
                        min="1"
                    />

                    <x-input
                        type="number"
                        name="advance_booking_requirement"
                        label="Advance Booking Requirement (days)"
                        min="0"
                    />
                </div>

                <div class="mt-6">
                    <x-input
                        type="textarea"
                        name="rules"
                        label="Reservation Notes / Restrictions"
                        rows="3"
                    />
                </div>
            </div>

            <!-- 6. Facility Image -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">6. Facility Image</h2>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Facility Photo (upload feature coming soon)</label>
                <input type="file" name="photos[]" multiple class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300 disabled:opacity-50 disabled:pointer-events-none" disabled>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Photo upload will be implemented later</p>
            </div>

            <!-- 7. Management Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">7. Management Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input
                        type="text"
                        name="person_in_charge"
                        label="Office / Person in Charge"
                    />

                    <x-input
                        type="text"
                        name="contact_information"
                        label="Contact Information"
                    />
                </div>

                <div class="mt-6">
                    <x-input
                        type="textarea"
                        name="special_instructions"
                        label="Special Instructions"
                        rows="3"
                    />
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('facilities.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Facility
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
