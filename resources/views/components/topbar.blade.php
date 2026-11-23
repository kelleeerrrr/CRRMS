@php
    $user = auth()->user();
    $unreadNotifications = $user ? $user->notifications()->where('is_read', false)->orderBy('created_at', 'desc')->take(5)->get() : collect();
    $unreadCount = $user ? $user->notifications()->where('is_read', false)->count() : 0;
@endphp

<header class="relative z-10 h-16 shrink-0 flex items-center justify-between px-4 sm:px-6 shadow-sm border-b" style="background: var(--topbar-bg); border-color: var(--sidebar-border);">
    <!-- Left Side -->
    <div class="flex items-center space-x-4">
        <!-- Mobile Menu Button -->
        <button type="button" aria-label="Toggle navigation" aria-controls="app-sidebar" :aria-expanded="mobileSidebarOpen" @click="mobileSidebarOpen = !mobileSidebarOpen" class="lg:hidden transition-colors" style="color: white;" @mouseover="this.style.color='rgba(255,255,255,0.8)'" @mouseout="this.style.color='white'">
            <x-heroicon-o-bars-3 class="w-6 h-6" />
        </button>
    </div>

    <!-- Right Side -->
    <div class="flex items-center space-x-3">
        <!-- Dark Mode Toggle -->
        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2 rounded-xl transition-colors" style="color: white;" @mouseover="this.style.background='rgba(255,255,255,0.1)'" @mouseout="this.style.background='transparent'">
            <x-heroicon-o-moon class="w-5 h-5 dark:hidden" />
            <x-heroicon-o-sun class="w-5 h-5 hidden dark:block" />
        </button>

        <livewire:topbar-messages />

        <!-- Notifications -->
        <div class="relative" x-data="{ open: false }">
            <button type="button" aria-label="Notifications" title="Notifications" :aria-expanded="open" @click="open = !open" @click.outside="open = false" class="relative p-2 rounded-xl transition-colors" style="color: white;" @mouseover="this.style.background='rgba(255,255,255,0.1)'" @mouseout="this.style.background='transparent'">
                <x-heroicon-o-bell class="w-5 h-5" />
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 rounded-full" style="background: var(--brand-maroon);">
                        {{ $unreadCount }}
                    </span>
                @endif
            </button>

            <!-- Notification Dropdown -->
            <div x-show="open" x-transition class="absolute right-0 mt-2 w-80 rounded-xl shadow-lg border z-50" style="background: var(--input-bg); border-color: var(--card-border);">
                <div class="p-4 border-b flex items-center justify-between" style="border-color: var(--card-border);">
                    <h3 class="font-semibold" style="color: var(--text-primary);">Notifications</h3>
                    @if($unreadCount > 0)
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full" style="background: var(--badge-rejected-bg); color: var(--badge-rejected-text);">{{ $unreadCount }}</span>
                    @endif
                </div>
                <div class="max-h-96 overflow-y-auto">
                    @if($unreadNotifications->isEmpty())
                        <div class="p-4 text-center" style="color: var(--text-muted);">
                            <p>No new notifications</p>
                        </div>
                    @else
                        <div class="divide-y" style="border-color: var(--card-border);">
                            @foreach($unreadNotifications as $notification)
                                <a href="{{ route('notifications.show', $notification) }}" class="block p-4 transition-colors" @mouseover="this.style.background='rgba(0,0,0,0.03)'" @mouseout="this.style.background='transparent'">
                                    <p class="text-sm font-medium" style="color: var(--text-primary);">{{ $notification->title }}</p>
                                    <p class="text-xs mt-1" style="color: var(--text-secondary);">{{ Str::limit($notification->message, 50) }}</p>
                                    <p class="text-xs mt-2" style="color: var(--text-muted);">{{ $notification->created_at->diffForHumans() }}</p>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="p-4 border-t" style="border-color: var(--card-border);">
                    <a href="{{ route('notifications.index') }}" class="text-sm font-medium" style="color: var(--brand-maroon);">View all notifications</a>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false" class="flex items-center space-x-2 p-1.5 rounded-xl transition-colors" @mouseover="this.style.background='rgba(0,0,0,0.05)'" @mouseout="this.style.background='transparent'">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white font-semibold text-sm shadow-sm" style="background: var(--brand-maroon);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-sm font-medium" style="color: var(--text-primary);">{{ $user->name }}</p>
                    <p class="text-xs" style="color: var(--text-secondary);">{{ $user->roles->first()->display_name ?? 'User' }}</p>
                </div>
                <x-heroicon-o-chevron-down class="w-4 h-4 hidden md:block" style="color: var(--text-muted);" />
            </button>

            <!-- User Dropdown -->
            <div x-show="open" x-transition class="absolute right-0 mt-2 w-56 rounded-xl shadow-lg border z-50" style="background: var(--input-bg); border-color: var(--card-border);">
                <div class="p-4 border-b" style="border-color: var(--card-border);">
                    <p class="font-medium" style="color: var(--text-primary);">{{ $user->name }}</p>
                    <p class="text-sm" style="color: var(--text-secondary);">{{ $user->email }}</p>
                </div>
                <div class="py-2">
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm transition-colors" style="color: var(--text-nav);" @mouseover="this.style.background='rgba(0,0,0,0.05)'" @mouseout="this.style.background='transparent'">
                        <x-heroicon-o-user class="w-5 h-5 mr-3" />
                        Profile
                    </a>
                </div>
                <div class="border-t py-2" style="border-color: var(--card-border);">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 text-sm transition-colors" style="color: var(--badge-rejected-text);" @mouseover="this.style.background='rgba(0,0,0,0.05)'" @mouseout="this.style.background='transparent'">
                            <x-heroicon-o-arrow-right class="w-5 h-5 mr-3" />
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
