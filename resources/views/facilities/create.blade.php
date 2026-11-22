<x-app-layout>
    @php($editing = isset($facility))
    <x-slot name="title">{{ $editing ? 'Edit Facility' : 'Add Facility' }}</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit Facility' : 'Add Facility' }}</h1>
        <p class="text-gray-600 dark:text-gray-400">{{ $editing ? 'Update facility details' : 'Create a new facility' }}</p>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ $editing ? route('facilities.update', $facility) : route('facilities.store') }}" enctype="multipart/form-data">
            @csrf
            @if($editing)
                @method('PUT')
            @endif

            <x-input-error :messages="$errors->all()" class="mb-4" />
            
            <!-- 1. Basic Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">1. Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input
                        type="text"
                        name="name"
                        :value="$facility->name ?? null"
                        label="Facility Name"
                        required
                    />

                    <x-select
                        name="type"
                        :value="$facility->type ?? null"
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
                    <x-textarea
                                                name="location_address"
                                                :value="$facility->location_address ?? null"
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
                        :value="$facility->capacity ?? null"
                        label="Capacity"
                        required
                        min="1"
                    />
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Amenities / Features</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @php($selectedAmenities = old('amenities', $errors->any() ? [] : ($facility->amenities ?? [])))
                        @foreach(array_unique(array_merge(['Projector', 'Air Conditioning', 'Wi-Fi', 'Whiteboard', 'Computers', 'Sound System'], $facility->amenities ?? [])) as $amenity)
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}" @checked(in_array($amenity, $selectedAmenities)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $amenity }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 4. Availability -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">4. Availability</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input
                        type="text"
                        name="operating_days"
                        :value="$facility->operating_days ?? null"
                        label="Operating Days"
                        placeholder="e.g., Monday - Friday"
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
                        :value="$facility->max_reservation_duration ?? null"
                        label="Maximum Reservation Duration (days)"
                        min="1"
                    />

                    <x-input
                        type="number"
                        name="advance_booking_requirement"
                        :value="$facility->advance_booking_requirement ?? null"
                        label="Advance Booking Requirement (days)"
                        min="0"
                    />
                </div>

                <div class="mt-6">
                    <x-textarea
                                                name="rules"
                                                :value="$facility->rules ?? null"
                        label="Reservation Notes / Restrictions"
                        rows="3"
                    />
                </div>
            </div>

            <!-- 6. Facility Image -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">6. Facility Image</h2>
                <x-image-picker name="photos" label="Add facility photos" :saved="$facility->photos ?? []" multiple />
            </div>

            <!-- 7. Management Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">7. Management Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input
                        type="text"
                        name="person_in_charge"
                        :value="$facility->person_in_charge ?? null"
                        label="Office / Person in Charge"
                    />

                    <x-input
                        type="text"
                        name="contact_information"
                        :value="$facility->contact_information ?? null"
                        label="Contact Information"
                    />
                </div>

                <div class="mt-6">
                    <x-textarea
                                                name="special_instructions"
                                                :value="$facility->special_instructions ?? null"
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
                    {{ $editing ? 'Save Changes' : 'Create Facility' }}
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
