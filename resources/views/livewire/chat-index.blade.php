<div x-data="{
    refreshInterval: null,
    init() {
        this.refreshInterval = setInterval(() => {
            this.$wire.refresh();
        }, 3000);
    },
    destroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    }
}" wire:poll.3000s>
    <!-- Filters -->
    <x-card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input
                type="text"
                wire:model.live="search"
                placeholder="Search chats..."
                label="Search"
                name="search"
            />
            
            <x-select
                wire:model.live="filter"
                label="Filter"
                name="filter"
                :options="[
                    'all' => 'All Chats',
                    'private' => 'Private',
                    'group' => 'Group'
                ]"
            />
        </div>
    </x-card>

    <!-- Chat List -->
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Chats ({{ $chats->count() }})
            </h3>
            @if($unreadCount > 0)
                <span class="px-3 py-1 text-sm font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">
                    {{ $unreadCount }} unread
                </span>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($chats as $chat)
                <a href="{{ route('chat.show', $chat) }}" class="block p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="flex-shrink-0">
                                @if($chat->type === 'group')
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-semibold">
                                        <x-heroicon-o-users class="w-6 h-6" />
                                    </div>
                                @else
                                    @php
                                        $otherParticipant = $chat->participants->where('id', '!=', auth()->id())->first();
                                    @endphp
                                    @if($otherParticipant)
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                            {{ strtoupper(substr($otherParticipant->name, 0, 1)) }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $chat->type === 'group' ? $chat->name : ($chat->participants->where('id', '!=', auth()->id())->first()?->name ?? 'Unknown') }}
                                    </p>
                                    @if($chat->last_message_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $chat->last_message_at->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    @php
                                        $lastMessage = $chat->messages->first();
                                        $isUnread = $lastMessage && 
                                                     $lastMessage->sender_id !== auth()->id() && 
                                                     (!$chat->current_participant?->pivot->last_read_at || 
                                                      $chat->current_participant->pivot->last_read_at < $lastMessage->created_at);
                                    @endphp
                                    <p class="text-sm {{ $isUnread ? 'font-bold text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }} truncate">
                                        @if($lastMessage && $lastMessage->sender_id === auth()->id())
                                            You: {{ $lastMessage->content }}
                                        @else
                                            {{ $lastMessage?->content ?? 'No messages yet' }}
                                        @endif
                                    </p>
                                    @if($chat->type === 'group')
                                        <span class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $chat->participants->count() }} members
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resource Details -->
                        @if($chat->resource_type && $chat->resource)
                            <div class="ml-4 pl-4 border-l border-gray-200 dark:border-gray-600">
                                @if($chat->resource_type === 'facility')
                                    <div class="text-right">
                                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $chat->facility->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Capacity: {{ $chat->facility->capacity }}</p>
                                    </div>
                                @elseif($chat->resource_type === 'equipment')
                                    <div class="text-right">
                                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $chat->equipment->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Qty: {{ $chat->equipment->available_quantity }}/{{ $chat->equipment->total_quantity }}</p>
                                    </div>
                                @elseif($chat->resource_type === 'resource')
                                    <div class="text-right">
                                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $chat->resource->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Available: {{ $chat->resource->available_quantity }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <x-empty-state title="No chats found" description="Start a conversation with someone." />
            @endforelse
        </div>
    </x-card>
</div>
