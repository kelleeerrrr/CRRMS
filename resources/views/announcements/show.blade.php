<x-slot name="title">{{ $announcement->title }}</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Announcements', 'url' => route('announcements.index')],
        ['title' => $announcement->title, 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $announcement->title }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ ucfirst($announcement->category) }} Announcement</p>
        </div>
        <div class="flex items-center space-x-3">
            @if($announcement->is_pinned)
                <form action="{{ route('announcements.unpin', $announcement) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        Unpin
                    </button>
                </form>
            @else
                <form action="{{ route('announcements.pin', $announcement) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        Pin
                    </button>
                </form>
            @endif
            <a href="{{ route('announcements.edit', $announcement) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Edit
            </a>
            <a href="{{ route('announcements.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Back
            </a>
        </div>
    </div>
</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Announcement Details -->
        <x-card>
            <div class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    @if($announcement->category === 'urgent')
                        <span class="px-3 py-1 text-sm font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Urgent</span>
                    @elseif($announcement->category === 'event')
                        <span class="px-3 py-1 text-sm font-semibold text-purple-800 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-100">Event</span>
                    @elseif($announcement->category === 'information')
                        <span class="px-3 py-1 text-sm font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-100">Information</span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">General</span>
                    @endif
                    
                    @if($announcement->is_pinned)
                        <span class="flex items-center text-yellow-600 dark:text-yellow-400">
                            <x-heroicon-o-pin class="w-4 h-4 mr-1" />
                            Pinned
                        </span>
                    @endif
                </div>
                
                <div class="prose dark:prose-invert max-w-none">
                    {{ $announcement->content }}
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Published By</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $announcement->createdBy->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Published At</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $announcement->published_at->format('F j, Y, g:i A') }}</p>
                    </div>
                    @if($announcement->expires_at)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Expires At</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $announcement->expires_at->format('F j, Y, g:i A') }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Target Audience</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($announcement->audience) }}</p>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Attachments -->
        @if($announcement->attachments->count() > 0)
            <x-card title="Attachments">
                <div class="space-y-2">
                    @foreach($announcement->attachments as $attachment)
                        <a href="{{ asset($attachment->file_path) }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <x-heroicon-o-paperclip class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-3" />
                            <span class="text-sm text-gray-900 dark:text-white">{{ $attachment->file_name }}</span>
                        </a>
                    @endforeach
                </div>
            </x-card>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <x-card title="Quick Actions">
            <div class="space-y-2">
                <a href="{{ route('announcements.index') }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-3" />
                    Back to Announcements
                </a>
                <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="inline w-full" onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                        <x-heroicon-o-trash class="w-5 h-5 mr-3" />
                        Delete
                    </button>
                </form>
            </div>
        </x-card>

        <!-- Target Audience Info -->
        <x-card title="Target Audience">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-4">
                    <x-heroicon-o-users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($announcement->target_audience) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Target audience</p>
                </div>
            </div>
        </x-card>
    </div>
</div>
