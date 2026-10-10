<div>
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
                    <div class="flex items-center">
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
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    {{ $chat->lastMessage?->message ?? 'No messages yet' }}
                                </p>
                                @if($chat->type === 'group')
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $chat->participants->count() }} members
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <x-empty-state title="No chats found" description="Start a conversation with someone." />
            @endforelse
        </div>
    </x-card>
</div>
