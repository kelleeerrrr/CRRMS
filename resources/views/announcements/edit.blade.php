<x-slot name="title">Edit Announcement</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Announcements', 'url' => route('announcements.index')],
        ['title' => 'Edit', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Announcement</h1>
    <p class="text-gray-600 dark:text-gray-400">Update announcement details</p>
</x-slot>

<x-card>
    <form action="{{ route('announcements.update', $announcement) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <x-input
                type="text"
                name="title"
                label="Title"
                placeholder="e.g., Campus Closure Notice"
                required
                :value="old('title', $announcement->title)"
                :error="$errors->first('title')"
            />
            
            <x-textarea
                name="content"
                label="Content"
                placeholder="Write your announcement content..."
                rows="6"
                required
                :value="old('content', $announcement->content)"
                :error="$errors->first('content')"
            />
            
            <x-select
                name="category"
                label="Category"
                :options="[
                    'general' => 'General',
                    'urgent' => 'Urgent',
                    'event' => 'Event',
                    'information' => 'Information'
                ]"
                :value="old('category', $announcement->category)"
                :error="$errors->first('category')"
            />
            
            <x-select
                name="audience"
                label="Target Audience"
                :options="[
                    'all' => 'All Users',
                    'students' => 'Students',
                    'faculty' => 'Faculty',
                    'staff' => 'Staff',
                    'specific_departments' => 'Specific Departments',
                    'specific_organizations' => 'Specific Organizations'
                ]"
                :value="old('audience', $announcement->audience)"
                :error="$errors->first('audience')"
            />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    type="date"
                    name="published_at"
                    label="Publish Date (Optional)"
                    :value="old('published_at', $announcement->published_at?->format('Y-m-d'))"
                    :error="$errors->first('published_at')"
                />
                
                <x-input
                    type="date"
                    name="expires_at"
                    label="Expiry Date (Optional)"
                    :value="old('expires_at', $announcement->expires_at?->format('Y-m-d'))"
                    :error="$errors->first('expires_at')"
                />
            </div>
            
            <label class="flex items-center">
                <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned', $announcement->is_pinned) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Pin this announcement</span>
            </label>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('announcements.show', $announcement) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Announcement
            </button>
        </div>
    </form>
</x-card>
