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
            'title' => 'Announcements',
            'icon' => 'heroicon-o-megaphone',
            'route' => 'announcements.index',
            'active' => str_starts_with($currentRoute, 'announcements'),
        ],
        [
            'title' => 'RESOURCES',
            'icon' => 'heroicon-o-cube',
            'items' => [
                ['title' => 'School Facilities', 'route' => 'facilities.index', 'active' => $currentRoute === 'facilities.index'],
                ['title' => 'School Equipment', 'route' => 'equipment.index', 'active' => $currentRoute === 'equipment.index'],
                ['title' => 'Other Resources', 'route' => 'resources.index', 'active' => str_starts_with($currentRoute, 'resources') && $currentRoute !== 'resources.my-lending'],
            ],
        ],
        [
            'title' => 'Requests',
            'icon' => 'heroicon-o-clipboard-document-list',
            'route' => 'borrower.requests',
            'active' => $currentRoute === 'borrower.requests',
        ],
        [
            'title' => 'Approved',
            'icon' => 'heroicon-o-check-circle',
            'route' => 'borrower.approved',
            'active' => $currentRoute === 'borrower.approved',
        ],
        [
            'title' => 'Borrowing Records',
            'icon' => 'heroicon-o-document-text',
            'route' => 'borrower.loans',
            'active' => $currentRoute === 'borrower.loans',
        ],
        [
            'title' => 'Transaction History',
            'icon' => 'heroicon-o-clock',
            'route' => 'borrower.history',
            'active' => $currentRoute === 'borrower.history',
        ],
        [
            'title' => 'Report an Issue',
            'icon' => 'heroicon-o-exclamation-triangle',
            'route' => 'borrower.issues.index',
            'active' => $currentRoute === 'borrower.issues.index',
        ],
        [
            'title' => 'Calendar',
            'icon' => 'heroicon-o-calendar',
            'route' => 'calendar.index',
            'active' => $currentRoute === 'calendar.index',
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
            'title' => 'Announcements',
            'icon' => 'heroicon-o-megaphone',
            'route' => 'announcements.index',
            'active' => str_starts_with($currentRoute, 'announcements'),
        ],
        [
            'title' => 'Manage',
            'icon' => 'heroicon-o-cube',
            'items' => [
                ['title' => 'Inventory', 'route' => 'inventory.index', 'active' => $currentRoute === 'inventory.index'],
                ['title' => 'School Facilities', 'route' => 'facilities.index', 'active' => $currentRoute === 'facilities.index'],
                ['title' => 'School Equipment', 'route' => 'equipment.index', 'active' => $currentRoute === 'equipment.index'],
                ['title' => 'School Resources', 'route' => 'resources.index', 'active' => $currentRoute === 'resources.index'],
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
            'title' => 'Announcements',
            'icon' => 'heroicon-o-megaphone',
            'route' => 'announcements.index',
            'active' => str_starts_with($currentRoute, 'announcements'),
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
            'title' => 'Announcements',
            'icon' => 'heroicon-o-megaphone',
            'route' => 'announcements.index',
            'active' => str_starts_with($currentRoute, 'announcements'),
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
    ];

    // Select menu based on role
    $menuItems = match($role) {
        'super_admin' => $superAdminMenuItems,
        'admin' => $adminMenuItems,
        'school_officer' => $schoolOfficerMenuItems,
        'user' => $userMenuItems,
        default => $userMenuItems,
    };

    if ($user->can('review-borrower-issues')) {
        $menuItems[] = [
            'title' => $role === 'school_officer' ? 'Reports' : 'Issue Reports',
            'icon' => 'heroicon-o-exclamation-triangle',
            'route' => 'borrower.issues.review',
            'active' => $currentRoute === 'borrower.issues.review',
        ];
    }
@endphp

<aside id="app-sidebar" class="w-64 max-w-full shrink-0 flex flex-col transition-all duration-300 fixed inset-y-0 left-0 lg:static lg:translate-x-0 z-30 h-full shadow-lg lg:shadow-none border-r" style="background: linear-gradient(180deg, var(--sidebar-bg-start), var(--sidebar-bg-end)); border-color: var(--sidebar-border);" :class="{ 'translate-x-0': mobileSidebarOpen, '-translate-x-full': !mobileSidebarOpen }">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 shrink-0 px-6 border-b" style="background: var(--brand-red); border-color: rgba(255,255,255,0.2);">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            <div class="w-9 h-9 bg-white rounded-full flex items-center justify-center shadow-sm">
                <img src="{{ asset('crrms logo.png') }}" alt="CRRMS Logo" class="w-7 h-7 object-contain">
            </div>
            <span class="font-semibold text-lg text-white">CRRMS</span>
        </a>
        <button type="button" aria-label="Close navigation" @click="mobileSidebarOpen = false" class="lg:hidden text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
            <x-heroicon-o-x-mark class="w-5 h-5" />
        </button>
    </div>

    <!-- Navigation -->
    <nav class="min-h-0 flex-1 overflow-y-auto overscroll-y-contain py-4 px-3 space-y-1">
        @foreach($menuItems as $item)
            @if(isset($item['items']))
                <!-- Dropdown Menu -->
                <div x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group" style="color: var(--nav-group-label); background: transparent;" @mouseover="this.style.background='rgba(255,255,255,0.05)'" @mouseout="this.style.background='transparent'">
                        <div class="flex items-center space-x-3">
                            <x-heroicon-{{ $item['icon'] }} class="w-5 h-5 shrink-0 transition-colors" style="color: var(--nav-group-label);" />
                            <span class="whitespace-nowrap uppercase text-xs font-semibold">{{ $item['title'] }}</span>
                        </div>
                        <x-heroicon-o-chevron-down class="w-4 h-4 shrink-0 transition-transform duration-200" style="color: var(--nav-group-label);" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" x-collapse class="mt-1 space-y-1">
                        @foreach($item['items'] as $subItem)
                            @if($subItem['route'] === '#')
                                <span class="flex items-center px-3 py-2 text-sm rounded-lg cursor-not-allowed" style="color: var(--nav-text);">
                                    <span class="ml-8 whitespace-nowrap">{{ $subItem['title'] }}</span>
                                </span>
                            @else
                                <a href="{{ route($subItem['route']) }}"
                                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-all duration-200 {{ $subItem['active'] ? 'font-semibold' : '' }}"
                                   @if($subItem['active'])
                                       style="background: var(--nav-active-bg); color: var(--nav-active-text); border-radius: 12px;"
                                   @else
                                       style="color: var(--nav-text); background: transparent;"
                                       @mouseover="this.style.background='rgba(255,255,255,0.05)'"
                                       @mouseout="this.style.background='transparent'"
                                   @endif
                                   >
                                    <span class="ml-8 whitespace-nowrap">{{ $subItem['title'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Single Menu Item -->
                <a href="{{ route($item['route']) }}"
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 group {{ $item['active'] ? 'font-semibold' : '' }}"
                   @if($item['active'])
                       style="background: var(--nav-active-bg); color: var(--nav-active-text); border-radius: 12px;"
                   @else
                       style="color: var(--nav-text); background: transparent;"
                       @mouseover="this.style.background='rgba(255,255,255,0.05)'"
                       @mouseout="this.style.background='transparent'"
                   @endif
                   >
                    <x-heroicon-{{ $item['icon'] }} class="w-5 h-5 shrink-0 transition-colors" @if($item['active']) style="color: var(--nav-active-text);" @else style="color: var(--nav-text);" @endif />
                    <span class="ml-3 whitespace-nowrap">{{ $item['title'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <!-- User Section -->
    <div class="shrink-0 border-t p-4" style="border-color: rgba(255,255,255,0.2);">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-semibold shadow-sm" style="background: var(--brand-red);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate" style="color: #FFFFFF;">{{ $user->name }}</p>
                <p class="text-xs truncate" style="color: #F6C9CD;">{{ $user->email }}</p>
            </div>
            @if(in_array($role, ['school_officer', 'admin', 'super_admin'], true))
                <a href="{{ route('profile.edit') }}" class="p-1.5 rounded-lg transition-colors" style="color: var(--nav-text);" @mouseover="this.style.background='rgba(255,255,255,0.1)'" @mouseout="this.style.background='transparent'">
                    <x-heroicon-o-wrench class="w-5 h-5" />
                </a>
            @endif
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Overlay -->
<div x-show="mobileSidebarOpen" style="display: none" x-transition.opacity class="fixed inset-0 bg-black/50 z-20 lg:hidden" @click="mobileSidebarOpen = false"></div>
