@php
    $user = auth()->user();
    $unreadNotifications = $user ? $user->notifications()->where('is_read', false)->orderBy('created_at', 'desc')->take(5)->get() : collect();
    $unreadCount = $user ? $user->notifications()->where('is_read', false)->count() : 0;
@endphp

<header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 sm:px-6 shadow-sm">
    <!-- Left Side -->
    <div class="flex items-center space-x-4">
        <!-- Mobile Menu Button -->
        <button @click="$dispatch('toggle-sidebar')" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <x-heroicon-o-bars-3 class="w-6 h-6" />
        </button>

        <!-- Search -->
        <div class="hidden md:block">
            <button @click="$dispatch('open-global-search')" class="flex items-center space-x-2 px-4 py-2 bg-gray-100 dark:bg-gray-700/50 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors group">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" />
                <span class="text-sm">Search...</span>
                <kbd class="hidden sm:inline-block px-2 py-0.5 text-xs font-semibold text-gray-400 bg-gray-200 dark:bg-gray-600 rounded-md">⌘K</kbd>
            </button>
        </div>
    </div>

    <!-- Right Side -->
    <div class="flex items-center space-x-3">
        <!-- Dark Mode Toggle -->
        <button @click="document.documentElement.classList.toggle('dark')" class="p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors">
            <x-heroicon-o-moon class="w-5 h-5 dark:hidden" />
            <x-heroicon-o-sun class="w-5 h-5 hidden dark:block" />
        </button>

        <!-- Notifications -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false" class="relative p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors">
                <x-heroicon-o-bell class="w-5 h-5" />
                @if($unreadCount > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-danger-500 rounded-full ring-2 ring-white dark:ring-gray-800"></span>
                @endif
            </button>

            <!-- Notification Dropdown -->
            <div x-show="open" x-transition class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                    @if($unreadCount > 0)
                        <span class="px-2.5 py-1 text-xs font-semibold text-danger-800 bg-danger-100 rounded-full dark:bg-danger-900 dark:text-danger-100">{{ $unreadCount }}</span>
                    @endif
                </div>
                <div class="max-h-96 overflow-y-auto">
                    @if($unreadNotifications->isEmpty())
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            <p>No new notifications</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($unreadNotifications as $notification)
                                <a href="{{ route('notifications.show', $notification) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($notification->message, 50) }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('notifications.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">View all notifications</a>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false" class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->roles->first()->display_name ?? 'User' }}</p>
                </div>
                <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-500 hidden md:block" />
            </button>

            <!-- User Dropdown -->
            <div x-show="open" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>
                <div class="py-2">
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <x-heroicon-o-user class="w-5 h-5 mr-3" />
                        Profile
                    </a>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 py-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-danger-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <x-heroicon-o-arrow-right class="w-5 h-5 mr-3" />
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
