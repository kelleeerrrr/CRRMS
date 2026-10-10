<div>
    <!-- Filters -->
    <x-card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-input
                type="text"
                wire:model.live="search"
                placeholder="Search announcements..."
                label="Search"
                name="search"
            />
            
            <x-select
                wire:model.live="category"
                label="Category"
                name="category"
                :options="[
                    '' => 'All Categories',
                    'general' => 'General',
                    'urgent' => 'Urgent',
                    'event' => 'Event',
                    'information' => 'Information'
                ]"
            />
            
            <x-select
                wire:model.live="audience"
                label="Target Audience"
                name="audience"
                :options="[
                    '' => 'All Audiences',
                    'all' => 'All',
                    'students' => 'Students',
                    'faculty' => 'Faculty',
                    'staff' => 'Staff',
                    'specific_departments' => 'Specific Departments',
                    'specific_organizations' => 'Specific Organizations'
                ]"
            />
            
            <x-select
                wire:model.live="is_pinned"
                label="Pinned Status"
                name="is_pinned"
                :options="[
                    '' => 'All',
                    '1' => 'Pinned Only',
                    '0' => 'Not Pinned'
                ]"
            />
        </div>
        
        <div class="mt-4 flex justify-end">
            <x-button variant="ghost" wire:click="resetFilters">
                Reset Filters
            </x-button>
        </div>
    </x-card>

    <!-- Announcements List -->
    <div class="space-y-4">
        @forelse($announcements as $announcement)
            <x-card class="hover:shadow-lg transition-shadow">
                <div class="flex items-start space-x-4">
                    @if($announcement->is_pinned)
                        <div class="flex-shrink-0">
                            <x-heroicon-o-pin class="w-6 h-6 text-yellow-500" />
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $announcement->title }}
                            </h3>
                            <div class="flex items-center space-x-2">
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
                        
                        <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                            {{ Str::limit($announcement->content, 200) }}
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="flex items-center">
                                    <x-heroicon-o-user class="w-4 h-4 mr-1" />
                                    {{ $announcement->createdBy->name }}
                                </span>
                                <span class="flex items-center">
                                    <x-heroicon-o-calendar class="w-4 h-4 mr-1" />
                                    {{ $announcement->published_at->format('M j, Y') }}
                                </span>
                                @if($announcement->attachments->count() > 0)
                                    <span class="flex items-center">
                                        <x-heroicon-o-paperclip class="w-4 h-4 mr-1" />
                                        {{ $announcement->attachments->count() }} attachment(s)
                                    </span>
                                @endif
                            </div>
                            
                            <a href="{{ route('announcements.show', $announcement) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                Read More
                            </a>
                        </div>
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
