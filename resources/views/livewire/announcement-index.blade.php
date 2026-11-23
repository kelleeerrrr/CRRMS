<div>
    <!-- Announcements List -->
    <div class="space-y-4">
        @forelse($announcements as $announcement)
            <x-card class="hover:shadow-lg transition-shadow {{ $announcement->is_pinned ? 'border-2 border-yellow-400' : '' }}">
                @if($announcement->is_pinned)
                    <div class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 px-4 py-2 rounded-t-lg flex items-center">
                        <x-heroicon-o-pin class="w-5 h-5 mr-2" />
                        <span class="font-semibold text-sm">Pinned Announcement</span>
                    </div>
                @endif
                
                @if($announcement->photo)
                    <div class="mb-4 {{ $announcement->is_pinned ? 'mt-4' : '' }}">
                        <img src="{{ asset('storage/' . $announcement->photo) }}" alt="{{ $announcement->title }}" class="w-full h-auto max-h-96 object-cover rounded-lg">
                    </div>
                @endif
                
                <div class="flex items-start space-x-4">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $announcement->title }}
                            </h3>
                            <div class="flex items-center space-x-2">
                                @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('school_officer'))
                                    <a href="{{ route('announcements.edit', $announcement) }}" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                        Edit
                                    </a>
                                @endif
                                @if($announcement->category === 'urgent')
                                    <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-100">Urgent</span>
                                @elseif($announcement->category === 'event')
                                    <span class="px-2 py-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-100">Event</span>
                                @elseif($announcement->category === 'information')
                                    <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-100">Info</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-300">General</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-400 mb-4">
                            {{ $announcement->content }}
                        </div>
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <span class="flex items-center">
                                <x-heroicon-o-user class="w-4 h-4 mr-1" />
                                {{ $announcement->createdBy->name }}
                            </span>
                            <span class="flex items-center">
                                <x-heroicon-o-calendar class="w-4 h-4 mr-1" />
                                {{ $announcement->published_at->format('M j, Y') }}
                            </span>
                        </div>

                        @if(!empty($announcement->attachments))
                            <div class="space-y-2">
                                @foreach($announcement->attachments as $attachment)
                                    <a href="{{ asset($attachment['file_path']) }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <x-heroicon-o-paperclip class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-3" />
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $attachment['file_name'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>
        @empty
            <x-card>
                <x-empty-state title="No announcements found" description="Check back later for new announcements." />
            </x-card>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($announcements->hasPages())
        <div class="mt-6">
            {{ $announcements->links() }}
        </div>
    @endif
</div>
