<x-slot name="title">Edit Calendar Event</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Calendar', 'url' => route('calendar.index')],
        ['title' => 'Edit', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Calendar Event</h1>
    <p class="text-gray-600 dark:text-gray-400">Update calendar event details</p>
</x-slot>

<x-card>
    <form action="{{ route('calendar.update', $event) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <x-input
                type="text"
                name="title"
                label="Title"
                placeholder="e.g., Campus Event"
                required
                :value="old('title', $event->title)"
                :error="$errors->first('title')"
            />
            
            <x-textarea
                name="description"
                label="Description (Optional)"
                placeholder="Describe the event..."
                rows="4"
                :value="old('description', $event->description)"
                :error="$errors->first('description')"
            />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    type="date"
                    name="start_date"
                    label="Start Date"
                    required
                    :value="old('start_date', $event->start_date->format('Y-m-d'))"
                    :error="$errors->first('start_date')"
                />
                
                <x-input
                    type="date"
                    name="end_date"
                    label="End Date (Optional)"
                    :value="old('end_date', $event->end_date?->format('Y-m-d'))"
                    :error="$errors->first('end_date')"
                />
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    type="time"
                    name="start_time"
                    label="Start Time (Optional)"
                    :value="old('start_time', $event->start_time?->format('H:i'))"
                    :error="$errors->first('start_time')"
                />
                
                <x-input
                    type="time"
                    name="end_time"
                    label="End Time (Optional)"
                    :value="old('end_time', $event->end_time?->format('H:i'))"
                    :error="$errors->first('end_time')"
                />
            </div>
            
            <x-input
                type="text"
                name="location"
                label="Location (Optional)"
                placeholder="e.g., Main Hall"
                :value="old('location', $event->location)"
                :error="$errors->first('location')"
            />
            
            <x-select
                name="event_type"
                label="Event Type"
                :options="[
                    'general' => 'General',
                    'reservation' => 'Reservation',
                    'borrow' => 'Equipment Borrow',
                    'maintenance' => 'Maintenance',
                    'event' => 'Event',
                    'holiday' => 'Holiday'
                ]"
                :value="old('event_type', $event->event_type)"
                :error="$errors->first('event_type')"
            />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_all_day" value="1" {{ old('is_all_day', $event->is_all_day) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">All Day Event</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring', $event->is_recurring) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Recurring Event</span>
                </label>
            </div>
            
            <x-select
                name="recurrence_pattern"
                label="Recurrence Pattern (Optional)"
                :options="[
                    '' => 'Select pattern',
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'yearly' => 'Yearly'
                ]"
                :value="old('recurrence_pattern', $event->recurrence_pattern)"
                :error="$errors->first('recurrence_pattern')"
            />
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('calendar.show', $event) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Event
            </button>
        </div>
    </form>
</x-card>
