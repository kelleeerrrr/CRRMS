<x-slot name="title">{{ $chat->type === 'group' ? $chat->name : ($chat->participants->where('id', '!=', auth()->id())->first()?->name ?? 'Chat') }}</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Chat', 'url' => route('chat.index')],
        ['title' => $chat->type === 'group' ? $chat->name : ($chat->participants->where('id', '!=', auth()->id())->first()?->name ?? 'Chat'), 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            @if($chat->type === 'group')
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-semibold mr-3">
                    <x-heroicon-o-users class="w-5 h-5" />
                </div>
            @else
                @php
                    $otherParticipant = $chat->participants->where('id', '!=', auth()->id())->first();
                @endphp
                @if($otherParticipant)
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold mr-3">
                        {{ strtoupper(substr($otherParticipant->name, 0, 1)) }}
                    </div>
                @endif
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $chat->type === 'group' ? $chat->name : ($otherParticipant?->name ?? 'Unknown') }}
                </h1>
                @if($chat->type === 'group')
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $chat->participants->count() }} members</p>
                @endif
            </div>
        </div>
        <div class="flex items-center space-x-3">
            @if($chat->type === 'group' && auth()->user()->can('update', $chat))
                <a href="{{ route('chat.edit', $chat) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Edit
                </a>
            @endif
            <a href="{{ route('chat.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Back
            </a>
        </div>
    </div>
</x-slot>

<div class="h-[calc(100vh-200px)] flex flex-col">
    <!-- Messages -->
    <div class="flex-1 overflow-y-auto space-y-4 mb-4" id="messages-container">
        @forelse($messages as $message)
            @if($message->type === 'system')
                <div class="flex justify-center">
                    <span class="px-3 py-1 text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 rounded-full">
                        {{ $message->message }}
                    </span>
                </div>
            @else
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md">
                        <div class="flex items-end {{ $message->sender_id === auth()->id() ? 'flex-row-reverse' : 'flex-row' }}">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm {{ $message->sender_id === auth()->id() ? 'ml-2' : 'mr-2' }}">
                                {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                            </div>
                            <div class="{{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white' }} rounded-lg px-4 py-2">
                                @if($message->type === 'file' && $message->file_path)
                                    <div class="flex items-center space-x-2">
                                        <x-heroicon-o-paper-clip class="w-5 h-5" />
                                        <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank" class="underline">
                                            {{ $message->file_name }}
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm">{{ $message->message }}</p>
                                @endif
                                <p class="text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-200' : 'text-gray-500' }} mt-1">
                                    {{ $message->created_at->format('H:i') }}
                                    @if($message->seenBy->contains(auth()->id()))
                                        <x-heroicon-o-check class="w-4 h-4 inline ml-1" />
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="text-center text-gray-500 dark:text-gray-400">
                <p>No messages yet. Start the conversation!</p>
            </div>
        @endforelse
    </div>

    <!-- Typing Indicator -->
    @if($typingUsers->count() > 0)
        <div class="mb-2 text-sm text-gray-500 dark:text-gray-400">
            @foreach($typingUsers as $user)
                {{ $user->name }} is typing...
            @endforeach
        </div>
    @endif

    <!-- Message Input -->
    <form action="{{ route('chat.send-message', $chat) }}" method="POST" class="flex items-center space-x-3">
        @csrf
        <div class="flex-1">
            <input type="text" name="message" placeholder="Type a message..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required>
        </div>
        <label class="cursor-pointer">
            <input type="file" name="file" class="hidden">
            <x-heroicon-o-paper-clip class="w-6 h-6 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" />
        </label>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <x-heroicon-o-paper-airplane class="w-5 h-5" />
        </button>
    </form>
</div>

<script>
    // Scroll to bottom on load
    document.getElementById('messages-container').scrollTop = document.getElementById('messages-container').scrollHeight;

    // Mark messages as seen
    @foreach($messages as $message)
        @if($message->sender_id !== auth()->id() && !$message->seenBy->contains(auth()->id()))
            fetch('{{ route('chat.mark-seen', $message) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        @endif
    @endforeach

    // Typing indicator
    const messageInput = document.querySelector('input[name="message"]');
    let typingTimeout;

    messageInput.addEventListener('input', () => {
        clearTimeout(typingTimeout);
        fetch('{{ route('chat.typing', $chat) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ is_typing: true })
        });

        typingTimeout = setTimeout(() => {
            fetch('{{ route('chat.typing', $chat) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ is_typing: false })
            });
        }, 1000);
    });
</script>
