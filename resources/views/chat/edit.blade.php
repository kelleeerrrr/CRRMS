<x-slot name="title">Edit Chat</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Chat', 'url' => route('chat.index')],
        ['title' => $chat->name ?? 'Chat', 'url' => route('chat.show', $chat)],
        ['title' => 'Edit', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Chat</h1>
    <p class="text-gray-600 dark:text-gray-400">Update group chat information</p>
</x-slot>

<x-card>
    <form action="{{ route('chat.update', $chat) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            @if($chat->type === 'group')
                <x-input
                    type="text"
                    name="name"
                    label="Group Name"
                    placeholder="e.g., Project Team"
                    :value="old('name', $chat->name)"
                    :error="$errors->first('name')"
                />
                
                <x-textarea
                    name="description"
                    label="Description"
                    placeholder="Group description..."
                    rows="3"
                    :value="old('description', $chat->description)"
                    :error="$errors->first('description')"
                />
            @endif
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Participants</h3>
                <div class="space-y-2">
                    @foreach($chat->participants as $participant)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm mr-3">
                                    {{ strtoupper(substr($participant->name, 0, 1)) }}
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $participant->name }}</span>
                            </div>
                            @if($participant->id !== auth()->id())
                                <form action="{{ route('chat.remove-participant', $chat) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this participant?')">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $participant->id }}">
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Remove
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Add Participant</h3>
                <div class="flex items-center space-x-3">
                    <x-select
                        name="user_id"
                        label="Select User"
                        :options="['' => 'Select a user'] + $users->whereNotIn('id', $chat->participants->pluck('id'))->pluck('name', 'id')->toArray()"
                    />
                    <form action="{{ route('chat.add-participant', $chat) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Add
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('chat.show', $chat) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Chat
            </button>
        </div>
    </form>
</x-card>
