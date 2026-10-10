<x-slot name="title">{{ $notification->title }}</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Notifications', 'url' => route('notifications.index')],
        ['title' => $notification->title, 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $notification->title }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ $notification->created_at->format('F j, Y, g:i A') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            @if(!$notification->is_read)
                <form action="{{ route('notifications.mark-read', $notification) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Mark as Read
                    </button>
                </form>
            @endif
            <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this notification?')">
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
    <div class="space-y-4">
        <div class="flex items-center space-x-2 mb-4">
            @if($notification->type === 'success')
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                <span class="text-sm font-medium text-green-800 dark:text-green-400">Success</span>
            @elseif($notification->type === 'warning')
                <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                <span class="text-sm font-medium text-yellow-800 dark:text-yellow-400">Warning</span>
            @elseif($notification->type === 'error')
                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                <span class="text-sm font-medium text-red-800 dark:text-red-400">Error</span>
            @else
                <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                <span class="text-sm font-medium text-blue-800 dark:text-blue-400">Info</span>
            @endif
            
            @if($notification->is_read)
                <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">Read</span>
            @else
                <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-100">Unread</span>
            @endif
        </div>
        
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Message</h3>
            <p class="text-gray-700 dark:text-gray-300">{{ $notification->message }}</p>
        </div>
        
        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Sent At</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification->created_at->format('F j, Y, g:i A') }}</p>
            </div>
            @if($notification->read_at)
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Read At</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification->read_at->format('F j, Y, g:i A') }}</p>
                </div>
            @endif
        </div>
        
        @if($notification->sent_via_email || $notification->sent_via_push)
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Delivery Method</h3>
                <div class="flex items-center space-x-4">
                    @if($notification->sent_via_email)
                        <span class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-o-envelope class="w-4 h-4 mr-1" />
                            Email
                        </span>
                    @endif
                    @if($notification->sent_via_push)
                        <span class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <x-heroicon-o-bell class="w-4 h-4 mr-1" />
                            Push
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-card>
