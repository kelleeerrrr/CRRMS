@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
    $role = $user->roles()->first()?->name ?? 'user';

    // USER navigation
    $userMenuItems = [
        [
            'title' => 'Dashboard',
            'icon' => 'heroicon-o-home',
            'route' => 'dashboard',
            'active' => $currentRoute === 'dashboard',
        ],
        [
            'title' => 'RESOURCES',
            'icon' => 'heroicon-o-cube',
            'items' => [
                ['title' => 'School Facilities', 'route' => 'facilities.index', 'active' => $currentRoute === 'facilities.index'],
                ['title' => 'School Equipment', 'route' => 'equipment.index', 'active' => $currentRoute === 'equipment.index'],
                ['title' => 'Other Resources', 'route' => 'resources.index', 'active' => str_starts_with($currentRoute, 'resources') && !str_starts_with($currentRoute, 'my-lending')],
                ['title' => 'My Requests', 'route' => 'my-lending.requests', 'active' => $currentRoute === 'my-lending.requests'],
                ['title' => 'My Borrowing', 'route' => 'my-lending.borrowing', 'active' => $currentRoute === 'my-lending.borrowing'],
                ['title' => 'My Lending', 'route' => 'resources.my-lending', 'active' => $currentRoute === 'resources.my-lending'],
            ],
        ],
        [
            'title' => 'Calendar',
            'icon' => 'heroicon-o-calendar',
            'route' => 'calendar.index',
            'active' => $currentRoute === 'calendar.index',
        ],
        [
            'title' => 'Chat',
            'icon' => 'heroicon-o-envelope',
            'route' => 'chat.index',
            'active' => str_starts_with($currentRoute, 'chat'),
        ],
        [
            'title' => 'Notifications',
            'icon' => 'heroicon-o-bell',
            'route' => 'notifications.index',
            'active' => str_starts_with($currentRoute, 'notifications'),
        ],
        [
            'title' => 'Announcements',
            'icon' => 'heroicon-o-megaphone',
            'route' => 'announcements.index',
            'active' => str_starts_with($currentRoute, 'announcements'),
        ],
        [
            'title' => 'Profile',
            'icon' => 'heroicon-o-user',
            'route' => 'profile.edit',
            'active' => $currentRoute === 'profile.edit',
        ],
    ];

    // SCHOOL OFFICER navigation
    $schoolOfficerMenuItems = [
        [
            'title' => 'Dashboard',
            'icon' => 'heroicon-o-home',
            'route' => 'dashboard',
            'active' => $currentRoute === 'dashboard',
        ],
        [
            'title' => 'Manage',
            'icon' => 'heroicon-o-cube',
            'items' => [
                ['title' => 'School Facilities', 'route' => 'facilities.index', 'active' => $currentRoute === 'facilities.index'],
                ['title' => 'School Equipment', 'route' => 'equipment.index', 'active' => $currentRoute === 'equipment.index'],
                ['title' => 'School Resources', 'route' => 'resources.index', 'active' => $currentRoute === 'resources.index'],
                ['title' => 'Inventory', 'route' => 'inventory.index', 'active' => $currentRoute === 'inventory.index'],
            ],
        ],
        [
            'title' => 'Request',
            'icon' => 'heroicon-o-clipboard-document-list',
            'items' => [
                ['title' => 'Facility Reservations', 'route' => 'facilities.reservations.index', 'active' => str_starts_with($currentRoute, 'facilities.reservations')],
                ['title' => 'Borrowing Requests', 'route' => 'equipment.borrow-requests.index', 'active' => str_starts_with($currentRoute, 'equipment.borrow-requests')],
                ['title' => 'Resource Requests', 'route' => 'resource-requests.index', 'active' => str_starts_with($currentRoute, 'resource-requests')],
            ],
        ],
        [
            'title' => 'Calendar',
            'icon' => 'heroicon-o-calendar',
            'route' => 'calendar.index',
            'active' => $currentRoute === 'calendar.index',
        ],
        [
            'title' => 'Chat',
            'icon' => 'heroicon-o-envelope',
            'route' => 'chat.index',
            'active' => str_starts_with($currentRoute, 'chat'),
        ],
        [
            'title' => 'Notifications',
            'icon' => 'heroicon-o-bell',
            'route' => 'notifications.index',
            'active' => str_starts_with($currentRoute, 'notifications'),
        ],
        [
            'title' => 'Announcements',
            'icon' => 'heroicon-o-megaphone',
            'route' => 'announcements.index',
            'active' => str_starts_with($currentRoute, 'announcements'),
        ],
    ];

    // ADMIN navigation
    $adminMenuItems = [
        [
            'title' => 'Dashboard',
            'icon' => 'heroicon-o-home',
            'route' => 'dashboard',
            'active' => $currentRoute === 'dashboard',
        ],
        [
            'title' => 'Facilities',
            'icon' => 'heroicon-o-building-office',
            'route' => 'facilities.reservations.index',
            'active' => str_starts_with($currentRoute, 'facilities.reservations'),
        ],
        [
            'title' => 'Equipment',
            'icon' => 'heroicon-o-cube',
            'route' => 'equipment.borrow-requests.index',
            'active' => str_starts_with($currentRoute, 'equipment.borrow-requests'),
        ],
        [
            'title' => 'Resources',
            'icon' => 'heroicon-o-document',
            'route' => 'resources.index',
            'active' => str_starts_with($currentRoute, 'resources'),
        ],
        [
            'title' => 'Announcements',
            'icon' => 'heroicon-o-megaphone',
            'route' => 'announcements.index',
            'active' => str_starts_with($currentRoute, 'announcements'),
        ],
    ];

    // SUPER ADMIN navigation
    $superAdminMenuItems = [
        [
            'title' => 'Dashboard',
            'icon' => 'heroicon-o-home',
            'route' => 'dashboard',
            'active' => $currentRoute === 'dashboard',
        ],
        [
            'title' => 'Facilities',
            'icon' => 'heroicon-o-building-office',
            'route' => 'facilities.reservations.index',
            'active' => str_starts_with($currentRoute, 'facilities.reservations'),
        ],
        [
            'title' => 'Equipment',
            'icon' => 'heroicon-o-cube',
            'route' => 'equipment.borrow-requests.index',
            'active' => str_starts_with($currentRoute, 'equipment.borrow-requests'),
        ],
        [
            'title' => 'Resources',
            'icon' => 'heroicon-o-document',
            'route' => 'resources.index',
            'active' => str_starts_with($currentRoute, 'resources'),
        ],
        [
            'title' => 'Announcements',
            'icon' => 'heroicon-o-megaphone',
            'route' => 'announcements.index',
            'active' => str_starts_with($currentRoute, 'announcements'),
        ],
    ];

    // Select menu based on role
    $menuItems = match($role) {
        'super_admin' => $superAdminMenuItems,
        'admin' => $adminMenuItems,
        'school_officer' => $schoolOfficerMenuItems,
        'user' => $userMenuItems,
        default => $userMenuItems,
    };
@endphp

<aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col transition-all duration-300 lg:translate-x-0 -translate-x-full fixed lg:relative z-30 h-full shadow-lg lg:shadow-none" x-data="{ open: false, mobileOpen: false }">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-primary-600 to-primary-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center shadow-sm">
                <span class="text-primary-600 font-bold text-sm">CR</span>
            </div>
            <span class="font-semibold text-lg text-white">CRRMS</span>
        </a>
        <button @click="mobileOpen = false" class="lg:hidden text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
            <x-heroicon-o-x-mark class="w-5 h-5" />
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        @foreach($menuItems as $item)
            @if(isset($item['items']))
                <!-- Dropdown Menu -->
                <div x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 group">
                        <div class="flex items-center space-x-3">
                            <x-heroicon-{{ $item['icon'] }} class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" />
                            <span>{{ $item['title'] }}</span>
                        </div>
                        <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1">
                        @foreach($item['items'] as $subItem)
                            @if($subItem['route'] === '#')
                                <span class="flex items-center px-3 py-2 text-sm rounded-lg text-gray-400 dark:text-gray-500 cursor-not-allowed">
                                    <span class="ml-8">{{ $subItem['title'] }}</span>
                                </span>
                            @else
                                <a href="{{ route($subItem['route']) }}"
                                   class="flex items-center px-3 py-2 text-sm rounded-lg {{ $subItem['active'] ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400 font-medium' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-all duration-200">
                                    <span class="ml-8">{{ $subItem['title'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Single Menu Item -->
                <a href="{{ route($item['route']) }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ $item['active'] ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-all duration-200 group">
                    <x-heroicon-{{ $item['icon'] }} class="w-5 h-5 {{ $item['active'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400' }} transition-colors" />
                    <span class="ml-3">{{ $item['title'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <!-- User Section -->
    <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-gray-50/50 dark:bg-gray-800/50">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-semibold shadow-sm">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 p-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <x-heroicon-o-wrench class="w-5 h-5" />
            </a>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Overlay -->
<div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-20 lg:hidden" @click="mobileOpen = false"></div>
