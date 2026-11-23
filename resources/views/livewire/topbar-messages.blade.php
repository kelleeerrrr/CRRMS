<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false" wire:poll.15s>
    <button type="button" @click="open = !open; if (open) $wire.$refresh()" :aria-expanded="open"
        aria-label="{{ $unreadCount > 0 ? 'Messages, unread conversations' : 'Messages' }}" aria-controls="topbar-message-dropdown" title="Messages"
        class="relative p-2 rounded-xl transition-colors dark:text-gray-400 dark:hover:bg-gray-700" style="color: white;" @mouseover="this.style.background='rgba(255,255,255,0.1)'" @mouseout="this.style.background='transparent'">
        <x-heroicon-o-envelope class="w-5 h-5" />
        @if($unreadCount > 0)
            <span aria-hidden="true" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none transform translate-x-1/4 -translate-y-1/4 rounded-full" style="background: var(--message-badge-bg); color: var(--message-badge-text);">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <div id="topbar-message-dropdown" x-show="open" x-transition style="display: none;"
        class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-8rem)] bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Recent messages</h3>
        </div>

        <ul class="max-h-96 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentMessages as $message)
                <li wire:key="topbar-message-sender-{{ $message->sender_id }}">
                    <a href="{{ route('chat.show', $message->conversation_id) }}"
                        class="block p-4 transition-colors hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-primary-500 dark:hover:bg-gray-700">
                        <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                            {{ $message->sender?->name ?? 'Unknown sender' }}
                            @if($message->conversation?->name)
                                <span class="font-normal text-gray-500 dark:text-gray-400">· {{ $message->conversation->name }}</span>
                            @endif
                        </p>
                        <p class="truncate text-sm text-gray-500 dark:text-gray-400">{{ $message->content ?: ($message->file_name ?: 'Attachment') }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $message->created_at->diffForHumans() }}</p>
                    </a>
                </li>
            @empty
                <li class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">No incoming messages yet</li>
            @endforelse
        </ul>

        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('chat.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">View all messages</a>
        </div>
    </div>
</div>
