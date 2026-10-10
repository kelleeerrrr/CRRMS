<x-slot name="title">Edit Maintenance Log</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Maintenance', 'url' => route('maintenance.index')],
        ['title' => 'Edit', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Maintenance Log</h1>
    <p class="text-gray-600 dark:text-gray-400">Update maintenance record</p>
</x-slot>

<x-card>
    <form action="{{ route('maintenance.update', $maintenanceLog) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <x-select
                name="type"
                label="Type"
                :options="[
                    'equipment' => 'Equipment',
                    'facility' => 'Facility'
                ]"
                required
                :value="old('type', $maintenanceLog->type)"
                :error="$errors->first('type')"
            />
            
            <div x-show="type === 'equipment' || $maintenanceLog->type === 'equipment'">
                <x-select
                    name="maintainable_id"
                    label="Equipment"
                    :options="['' => 'Select equipment'] + $equipment->pluck('name', 'id')->toArray()"
                    required
                    :value="old('maintainable_id', $maintenanceLog->maintainable_id)"
                    :error="$errors->first('maintainable_id')"
                />
                <input type="hidden" name="maintainable_type" value="App\Models\Equipment">
            </div>
            
            <div x-show="type === 'facility' || $maintenanceLog->type === 'facility'">
                <x-select
                    name="maintainable_id"
                    label="Facility"
                    :options="['' => 'Select facility'] + $facilities->pluck('name', 'id')->toArray()"
                    required
                    :value="old('maintainable_id', $maintenanceLog->maintainable_id)"
                    :error="$errors->first('maintainable_id')"
                />
                <input type="hidden" name="maintainable_type" value="App\Models\Facility">
            </div>
            
            <x-input
                type="text"
                name="title"
                label="Title"
                placeholder="e.g., Replace broken part"
                required
                :value="old('title', $maintenanceLog->title)"
                :error="$errors->first('title')"
            />
            
            <x-textarea
                name="description"
                label="Description"
                placeholder="Describe the maintenance issue..."
                rows="4"
                required
                :value="old('description', $maintenanceLog->description)"
                :error="$errors->first('description')"
            />
            
            <x-select
                name="priority"
                label="Priority"
                :options="[
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'urgent' => 'Urgent'
                ]"
                :value="old('priority', $maintenanceLog->priority)"
                :error="$errors->first('priority')"
            />
            
            <x-select
                name="assigned_to"
                label="Assign To (Optional)"
                :options="['' => 'Select technician'] + $technicians->pluck('name', 'id')->toArray()"
                :value="old('assigned_to', $maintenanceLog->assigned_to)"
                :error="$errors->first('assigned_to')"
            />
            
            <x-input
                type="date"
                name="scheduled_date"
                label="Scheduled Date (Optional)"
                :value="old('scheduled_date', $maintenanceLog->scheduled_date?->format('Y-m-d'))"
                :error="$errors->first('scheduled_date')"
            />
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('maintenance.show', $maintenanceLog) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Maintenance Log
            </button>
        </div>
    </form>
</x-card>

<script>
    function setType(value) {
        document.querySelector('input[name="maintainable_type"]').value = value === 'equipment' ? 'App\\Models\\Equipment' : 'App\\Models\\Facility';
    }
</script>
