<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-dashboard.stat-card title="Total Users" :value="$stats['total_users']" icon="users" color="blue" />
        <x-dashboard.stat-card title="Active Users" :value="$stats['active_users']" icon="check-circle" color="green" />
        <x-dashboard.stat-card title="Inactive Users" :value="$stats['inactive_users']" icon="x-circle" color="red" />
        <x-dashboard.stat-card title="Total Roles" :value="$stats['total_roles']" icon="shield" color="purple" />
        <x-dashboard.stat-card title="Total Permissions" :value="$stats['total_permissions']" icon="key" color="orange" />
        <x-dashboard.stat-card title="System Activity" :value="$stats['system_activity']" icon="lightning" color="yellow" />
        <x-dashboard.stat-card title="Overall Reports" :value="$stats['overall_reports']" icon="document" color="blue" />
    </div>

    <!-- User Overview -->
    <x-card title="User Overview">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Users by Role</h4>
                <x-dashboard.donut-chart :data="$userOverview['users_by_role']" title="Distribution" />
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">User Status</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Active Users</span>
                        <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ $userOverview['active_users'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Inactive Users</span>
                        <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ $userOverview['inactive_users'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Total Users</span>
                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $userOverview['total_users'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Role & Permission Overview -->
    <x-card title="Role & Permission Overview">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Summary</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Total Roles</span>
                        <span class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $rolePermissionOverview['total_roles'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Total Permissions</span>
                        <span class="text-lg font-bold text-orange-600 dark:text-orange-400">{{ $rolePermissionOverview['total_permissions'] }}</span>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Roles & Permissions</h4>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($rolePermissionOverview['roles'] as $role)
                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $role['name'] }}</span>
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $role['permissions_count'] }} perms</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-card>

    <!-- System Activity -->
    <x-card title="System Activity">
        @if(empty($systemActivity))
            <x-empty-state title="No recent activity" description="No recent system activity recorded." />
        @else
            <div class="space-y-3">
                @foreach($systemActivity as $activity)
                    <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="w-10 h-10 rounded-full @if($activity['type'] === 'auth') bg-green-100 dark:bg-green-900 @elseif($activity['type'] === 'user_management') bg-purple-100 dark:bg-purple-900 @elseif($activity['type'] === 'configuration') bg-orange-100 dark:bg-orange-900 @else bg-blue-100 dark:bg-blue-900 @endif flex items-center justify-center mr-4">
                            @if($activity['type'] === 'auth')
                                <x-heroicon-o-key class="w-5 h-5 text-green-600 dark:text-green-400" />
                            @elseif($activity['type'] === 'user_management')
                                <x-heroicon-o-users class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                            @elseif($activity['type'] === 'configuration')
                                <x-heroicon-o-cog class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                            @else
                                <x-heroicon-o-lightning class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $activity['action'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $activity['description'] }} • {{ $activity['user'] }} • {{ $activity['time'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- System Administration Quick Actions -->
    <x-dashboard.quick-actions :actions="[
        ['title' => 'Manage Users', 'description' => 'View and manage user accounts', 'icon' => 'users', 'url' => '#'],
        ['title' => 'View Reservations', 'description' => 'View facility reservations', 'icon' => 'calendar', 'url' => route('facilities.reservations.index')],
        ['title' => 'View Resources', 'description' => 'View school resources', 'icon' => 'document', 'url' => route('resources.index')],
    ]" title="System Administration" />
</div>

