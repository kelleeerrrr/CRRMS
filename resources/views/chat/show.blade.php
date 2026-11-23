<x-app-layout>
    <x-slot name="fillHeight"></x-slot>
    <x-slot name="title">{{ $conversation->type === 'group' ? $conversation->name : ($conversation->participants->where('id', '!=', auth()->id())->first()?->name ?? 'Chat') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                @if($conversation->type === 'group')
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-semibold mr-3">
                        👥
                    </div>
                @else
                    @php
                        $otherParticipant = $conversation->participants->where('id', '!=', auth()->id())->first();
                    @endphp
                    @if($otherParticipant && $otherParticipant->profile_photo_path)
                        <img src="{{ asset('storage/' . $otherParticipant->profile_photo_path) }}" alt="{{ $otherParticipant->name }}" class="w-10 h-10 rounded-full object-cover mr-3">
                    @elseif($otherParticipant)
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold mr-3">
                            {{ strtoupper(substr($otherParticipant->name, 0, 1)) }}
                        </div>
                    @endif
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $conversation->type === 'group' ? $conversation->name : ($otherParticipant?->name ?? 'Unknown') }}
                    </h1>
                    @if($conversation->type === 'group')
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $conversation->participants->count() }} members</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-3">
                @if($conversation->type === 'group' && auth()->user()->can('update', $conversation))
                    <a href="{{ route('chat.edit', $conversation) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Edit
                    </a>
                @endif
                <a href="{{ route('chat.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="flex min-h-0 flex-1 flex-col">
        <div id="chat-thread-scroll" class="min-h-0 flex-1 overflow-y-auto overscroll-y-contain pb-4">
            <!-- Resource Information Card -->
            @if($conversation->resource_type && $conversation->resource)
                @if($conversation->resource_type === 'facility')
                    <x-facility-info-card :facility="$conversation->facility" :reservation="$conversation->facilityReservation" />
                @elseif($conversation->resource_type === 'equipment')
                    <x-equipment-info-card :equipment="$conversation->equipment" :borrowRequest="$conversation->equipmentBorrowRequest" />
                @elseif($conversation->resource_type === 'resource')
                    <x-resource-info-card :resource="$conversation->resource" :borrowRequest="$conversation->resourceBorrowRequest" />
                @endif
            @endif

    <div class="h-[calc(100vh-200px)] flex flex-col">
        <!-- Messages -->
        <div class="flex-1 overflow-y-auto space-y-4 mb-4" id="messages-container">
            @forelse($messages as $message)
                @if($message->type === 'system')
                    <div class="flex justify-center">
                        <span class="px-3 py-1 text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 rounded-full">
                            {{ $message->content }}
                        </span>
                    </div>
                @else
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="flex items-end {{ $message->sender_id === auth()->id() ? 'flex-row-reverse' : 'flex-row' }}">
                                @if($message->sender->profile_photo_path)
                                    <img src="{{ asset('storage/' . $message->sender->profile_photo_path) }}" alt="{{ $message->sender->name }}" class="w-8 h-8 rounded-full object-cover {{ $message->sender_id === auth()->id() ? 'ml-2' : 'mr-2' }}">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm {{ $message->sender_id === auth()->id() ? 'ml-2' : 'mr-2' }}">
                                        {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="{{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white' }} rounded-lg px-4 py-2">
                                    @if($message->type === 'file' && $message->file_path)
                                        <div class="flex items-center space-x-2">
                                            📎
                                            <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank" class="underline">
                                                {{ $message->file_name }}
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-sm">{{ $message->content }}</p>
                                    @endif
                                    <p class="text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-200' : 'text-gray-500' }} mt-1">
                                    {{ $message->created_at->format('H:i') }}
                                </p>
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
        </div>

        <div class="shrink-0 border-t border-gray-200 bg-gray-50 pb-3 pt-3 dark:border-gray-700 dark:bg-gray-900 sm:pb-4">
            <!-- Typing Indicator -->
            @if(count($typingUsers) > 0)
                <div class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                    @foreach($typingUsers as $user)
                        {{ $user->name }} is typing...
                    @endforeach
                </div>
            @endif

            <!-- Message Input -->
            <form action="{{ route('chat.send-message', $conversation) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 sm:gap-3" id="message-form" autocomplete="off">
                @csrf
                <div class="min-w-0 flex-1">
                    <input type="text" name="message" id="message-input" aria-label="Message" placeholder="Type a message..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" required autocomplete="off">
                </div>
                <label class="inline-flex shrink-0 cursor-pointer items-center rounded-lg p-2 focus-within:ring-2 focus-within:ring-blue-500">
                    <input type="file" name="file" class="sr-only" id="file-input" aria-label="Attach file">
                    <span class="w-6 h-6 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">📎</span>
                </label>
                <button type="submit" aria-label="Send message" class="shrink-0 px-4 py-2 sm:px-6 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    ✈️
                </button>
            </form>
        </div>
    </div>

    <script>
        // Scroll to bottom on load
        const threadScroll = document.getElementById('chat-thread-scroll');
        threadScroll.scrollTop = threadScroll.scrollHeight;

        // Clear input after page load (in case of refresh)
        document.getElementById('message-input').value = '';

        // Handle form submission with AJAX
        document.getElementById('message-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const messageInput = document.getElementById('message-input');
            const submitButton = this.querySelector('button[type="submit"]');
            
            // Disable button during submission
            submitButton.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear the input immediately
                    messageInput.value = '';
                    // Reload page to show new message
                    window.location.reload();
                } else {
                    alert('Error sending message: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending message');
            })
            .finally(() => {
                // Re-enable button
                submitButton.disabled = false;
            });
        });

        // Poll for new messages every 3 seconds
        let lastMessageCount = {{ $messages->count() }};
        
        setInterval(() => {
            fetch('{{ route("chat.show", $conversation) }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newMessages = doc.getElementById('messages-container');
                const currentMessages = document.getElementById('messages-container');
                
                if (newMessages && currentMessages) {
                    const newMessageCount = newMessages.children.length;
                    
                    if (newMessageCount > lastMessageCount) {
                        const nearBottom = threadScroll.scrollHeight - threadScroll.scrollTop - threadScroll.clientHeight < 48;
                        // Get only new messages
                        for (let i = lastMessageCount; i < newMessageCount; i++) {
                            if (newMessages.children[i]) {
                                currentMessages.appendChild(newMessages.children[i].cloneNode(true));
                            }
                        }
                        lastMessageCount = newMessageCount;
                        if (nearBottom) threadScroll.scrollTop = threadScroll.scrollHeight;
                    }
                }
            })
            .catch(error => console.error('Error polling for messages:', error));
        }, 3000);
    </script>
</x-app-layout>
