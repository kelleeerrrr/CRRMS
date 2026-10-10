<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-dashboard.stat-card title="Assigned Repairs" :value="$stats['assigned_repairs']" icon="wrench" color="blue" />
        <x-dashboard.stat-card title="Open Jobs" :value="$stats['open_jobs']" icon="clock" color="yellow" />
        <x-dashboard.stat-card title="Equipment Maintenance" :value="$stats['equipment_maintenance']" icon="cube" color="orange" />
        <x-dashboard.stat-card title="Facility Maintenance" :value="$stats['facility_maintenance']" icon="home" color="purple" />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <x-dashboard.donut-chart :data="$maintenanceCompletion" title="Maintenance Status" />
        <x-dashboard.activity-timeline :activities="$recentActivity" title="Recent Activity" />
    </div>

    <!-- Quick Actions -->
    <x-dashboard.quick-actions :actions="[
        ['title' => 'Update Repair Status', 'description' => 'Update status of assigned repairs', 'icon' => 'wrench', 'url' => route('maintenance.index')],
        ['title' => 'Complete Work Orders', 'description' => 'Mark work orders as complete', 'icon' => 'check', 'url' => route('maintenance.index')],
        ['title' => 'View Schedule', 'description' => 'View maintenance schedule', 'icon' => 'calendar', 'url' => route('calendar.index')],
        ['title' => 'Report Issue', 'description' => 'Report a new maintenance issue', 'icon' => 'document', 'url' => route('maintenance.create')],
    ]" title="Quick Actions" />
</div>
