<x-slot name="title">Create Notification</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Notifications', 'url' => route('notifications.index')],
        ['title' => 'Create', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Notification</h1>
    <p class="text-gray-600 dark:text-gray-400">Send a notification to a user</p>
</x-slot>

<x-card>
    <form action="{{ route('notifications.store') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            <x-select
                name="user_id"
                label="Recipient"
                :options="['' => 'Select a user'] + $users->pluck('name', 'id')->toArray()"
                required
                :value="old('user_id')"
                :error="$errors->first('user_id')"
            />
            
            <x-select
                name="type"
                label="Type"
                :options="[
                    'info' => 'Info',
                    'success' => 'Success',
                    'warning' => 'Warning',
                    'error' => 'Error'
                ]"
                required
                :value="old('type', 'info')"
                :error="$errors->first('type')"
            />
            
            <x-input
                type="text"
                name="title"
                label="Title"
                placeholder="Notification title"
                required
                :value="old('title')"
                :error="$errors->first('title')"
            />
            
            <x-textarea
                name="message"
                label="Message"
                placeholder="Notification message"
                rows="4"
                required
                :value="old('message')"
                :error="$errors->first('message')"
            />
            
            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input type="checkbox" name="sent_via_email" value="1" {{ old('sent_via_email') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Send via Email</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" name="sent_via_push" value="1" {{ old('sent_via_push') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Send via Push</span>
                </label>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('notifications.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Send Notification
            </button>
        </div>
    </form>
</x-card>
