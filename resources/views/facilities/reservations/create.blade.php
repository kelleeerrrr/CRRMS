<x-app-layout>
    <x-slot name="title">Create Reservation</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Reservation</h1>
        <p class="text-gray-600 dark:text-gray-400">Book a facility for your event or activity</p>
    </x-slot>

    <x-card>
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('facilities.reservations.store') }}" method="POST" id="reservationForm">
            @csrf
            <input type="hidden" name="facility_id" value="{{ $facilityId }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input
                    type="text"
                    name="reservee_name"
                    label="Reservee Name"
                    placeholder="Enter the name of the person reserving"
                    required
                    :value="old('reservee_name')"
                    :error="$errors->first('reservee_name')"
                />

                <x-input
                    type="text"
                    name="contact_number"
                    label="Contact Number"
                    placeholder="Enter contact number"
                    required
                    :value="old('contact_number')"
                    :error="$errors->first('contact_number')"
                />

                <x-input
                    type="date"
                    name="start_date"
                    label="Start Date"
                    required
                    :value="old('start_date')"
                    :error="$errors->first('start_date')"
                />

                <x-input
                    type="date"
                    name="end_date"
                    label="End Date"
                    required
                    :value="old('end_date')"
                    :error="$errors->first('end_date')"
                />

                <x-input
                    type="time"
                    name="start_time"
                    label="Start Time"
                    required
                    :value="old('start_time')"
                    :error="$errors->first('start_time')"
                />

                <x-input
                    type="time"
                    name="end_time"
                    label="End Time"
                    required
                    :value="old('end_time')"
                    :error="$errors->first('end_time')"
                />

                <x-select
                    name="organization_id"
                    label="Organization (Optional)"
                    :options="['' => 'Select an organization (optional)'] + \App\Models\Organization::pluck('name', 'id')->toArray()"
                    :value="old('organization_id')"
                />

                <x-input
                    type="number"
                    name="attendees_count"
                    label="Expected Attendees"
                    placeholder="Number of attendees"
                    :value="old('attendees_count')"
                    :error="$errors->first('attendees_count')"
                />
            </div>

            <div class="mt-6">
                <x-input
                    type="text"
                    name="purpose"
                    label="Purpose"
                    placeholder="e.g., Training, Meeting, Event"
                    :value="old('purpose')"
                    :error="$errors->first('purpose')"
                />
            </div>

            <div class="mt-6">
                <x-textarea
                    name="description"
                    label="Description"
                    placeholder="Provide details about your reservation..."
                    rows="4"
                    :value="old('description')"
                    :error="$errors->first('description')"
                />
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('facilities.reservations.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Reservation
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
