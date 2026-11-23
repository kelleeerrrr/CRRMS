<x-app-layout :breadcrumbs="[
        ['title' => 'Announcements', 'url' => route('announcements.index')],
        ['title' => 'Edit', 'url' => null],
    ]">
<x-slot name="title">Edit Announcement</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Announcement</h1>
            <p class="text-gray-600 dark:text-gray-400">Update announcement details</p>
        </div>
        <div class="flex items-center space-x-3">
            @if($announcement->is_pinned)
                <form action="{{ route('announcements.unpin', $announcement) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        Unpin
                    </button>
                </form>
            @else
                <form action="{{ route('announcements.pin', $announcement) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        Pin
                    </button>
                </form>
            @endif
            <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Delete
                </button>
            </form>
        </div>
    </div>
</x-slot>

<x-card>
    <form action="{{ route('announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data">
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
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Photo (Optional)</label>
                @if($announcement->photo)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $announcement->photo) }}" alt="Current photo" class="h-32 w-auto rounded-lg">
                    </div>
                @endif
                <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-blue-300">
                @error('photo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
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
            <a href="{{ route('announcements.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Announcement
            </button>
        </div>
    </form>
</x-card>
</x-app-layout>
