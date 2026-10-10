<div>
    <!-- Filters -->
    <x-card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-select
                wire:model.live="filter"
                label="Filter"
                name="filter"
                :options="[
                    'all' => 'All Notifications',
                    'unread' => 'Unread',
                    'read' => 'Read'
                ]"
            />
            
            <x-select
                wire:model.live="type"
                label="Type"
                name="type"
                :options="[
                    '' => 'All Types',
                    'info' => 'Info',
                    'success' => 'Success',
                    'warning' => 'Warning',
                    'error' => 'Error'
                ]"
            />
        </div>
        
        <div class="mt-4 flex justify-between items-center">
            @if($unreadCount > 0)
                <button WIre:click="markAllAsRead" class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    Mark all as read
                </button>
            @endif
        </div>
    </x-card>

    <!-- Notifications List -->
    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Notifications ({{ $notifications->total() }})
            </h3>
            @if($unreadCount > 0)
                <span class="px-3 py-1 text-sm font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">
                    {{ $unreadCount }} unread
                </span>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
                <div class="p-4 {{ $notification->is_read ? 'bg-gray-50 dark:bg-gray-700/30' : 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500' }} rounded-lg transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                @if($notification->type === 'success')
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                @elseif($notification->type === 'warning')
                                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                @elseif($notification->type === 'error')
                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                @else
                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                @endif
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $notification->title }}</h4>
                                @if(!$notification->is_read)
                                    <span class="px-2 py-0.5 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-100">New</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            @if(!$notification->is_read)
                                <button wire:click="markAsRead({{ $notification->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Mark as read">
                                    <x-heroicon-o-check class="w-5 h-5" />
                                </button>
                            @endif
                            <button wire:click="deleteNotification({{ $notification->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <x-empty-state title="No notifications" description="You're all caught up!" />
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </x-card>
</div>
