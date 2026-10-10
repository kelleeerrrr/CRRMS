<x-slot name="title">Edit Reservation</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Facilities', 'url' => '#'],
        ['title' => 'Reservations', 'url' => route('facilities.reservations.index')],
        ['title' => 'Edit', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Reservation</h1>
    <p class="text-gray-600 dark:text-gray-400">Update reservation details</p>
</x-slot>

<x-card>
    <form action="{{ route('facilities.reservations.update', $reservation) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-select
                name="facility_id"
                label="Facility"
                :options="['' => 'Select a facility'] + \App\Models\Facility::where('is_active', true)->pluck('name', 'id')->toArray()"
                required
                :value="old('facility_id', $reservation->facility_id)"
                :error="$errors->first('facility_id')"
            />
            
            <x-input
                type="text"
                name="title"
                label="Reservation Title"
                placeholder="e.g., Team Meeting, Training Session"
                required
                :value="old('title', $reservation->title)"
                :error="$errors->first('title')"
            />
            
            <x-input
                type="date"
                name="start_date"
                label="Start Date"
                required
                :value="old('start_date', $reservation->start_date->format('Y-m-d'))"
                :error="$errors->first('start_date')"
            />
            
            <x-input
                type="date"
                name="end_date"
                label="End Date"
                required
                :value="old('end_date', $reservation->end_date->format('Y-m-d'))"
                :error="$errors->first('end_date')"
            />
            
            <x-input
                type="time"
                name="start_time"
                label="Start Time"
                required
                :value="old('start_time', $reservation->start_time->format('H:i'))"
                :error="$errors->first('start_time')"
            />
            
            <x-input
                type="time"
                name="end_time"
                label="End Time"
                required
                :value="old('end_time', $reservation->end_time->format('H:i'))"
                :error="$errors->first('end_time')"
            />
            
            <x-select
                name="organization_id"
                label="Organization (Optional)"
                :options="['' => 'Select an organization (optional)'] + \App\Models\Organization::pluck('name', 'id')->toArray()"
                :value="old('organization_id', $reservation->organization_id)"
            />
            
            <x-input
                type="number"
                name="attendees_count"
                label="Expected Attendees"
                placeholder="Number of attendees"
                :value="old('attendees_count', $reservation->attendees_count)"
                :error="$errors->first('attendees_count')"
            />
        </div>
        
        <div class="mt-6">
            <x-input
                type="text"
                name="purpose"
                label="Purpose"
                placeholder="e.g., Training, Meeting, Event"
                :value="old('purpose', $reservation->purpose)"
                :error="$errors->first('purpose')"
            />
        </div>
        
        <div class="mt-6">
            <x-textarea
                name="description"
                label="Description"
                placeholder="Provide details about your reservation..."
                rows="4"
                :value="old('description', $reservation->description)"
                :error="$errors->first('description')"
            />
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('facilities.reservations.show', $reservation) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Reservation
            </button>
        </div>
    </form>
</x-card>
