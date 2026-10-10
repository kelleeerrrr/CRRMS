<x-slot name="title">New Chat</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Chat', 'url' => route('chat.index')],
        ['title' => 'New', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">New Chat</h1>
    <p class="text-gray-600 dark:text-gray-400">Start a new conversation</p>
</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Private Chat -->
    <x-card title="Private Chat">
        <form action="{{ route('chat.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="private">
            
            <div class="space-y-4">
                <x-select
                    name="participant_id"
                    label="Select User"
                    :options="['' => 'Select a user'] + $users->pluck('name', 'id')->toArray()"
                    required
                    :value="old('participant_id')"
                    :error="$errors->first('participant_id')"
                />
                
                <x-textarea
                    name="message"
                    label="Initial Message (Optional)"
                    placeholder="Type your first message..."
                    rows="4"
                    :value="old('message')"
                    :error="$errors->first('message')"
                />
                
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Start Private Chat
                </button>
            </div>
        </form>
    </x-card>

    <!-- Group Chat -->
    <x-card title="Group Chat">
        <form action="{{ route('chat.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="group">
            
            <div class="space-y-4">
                <x-input
                    type="text"
                    name="name"
                    label="Group Name"
                    placeholder="e.g., Project Team"
                    required
                    :value="old('name')"
                    :error="$errors->first('name')"
                />
                
                <x-textarea
                    name="description"
                    label="Description (Optional)"
                    placeholder="Group description..."
                    rows="3"
                    :value="old('description')"
                    :error="$errors->first('description')"
                />
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Participants</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($users as $user)
                            <label class="flex items-center p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                <input type="checkbox" name="participants[]" value="{{ $user->id }}" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ $user->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <x-textarea
                    name="message"
                    label="Initial Message (Optional)"
                    placeholder="Type your first message..."
                    rows="4"
                    :value="old('message')"
                    :error="$errors->first('message')"
                />
                
                <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Create Group Chat
                </button>
            </div>
        </form>
    </x-card>
</div>
