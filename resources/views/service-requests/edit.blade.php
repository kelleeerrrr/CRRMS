<x-slot name="title">Edit Service Request</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Service Requests', 'url' => route('service-requests.index')],
        ['title' => 'Edit', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Service Request</h1>
    <p class="text-gray-600 dark:text-gray-400">Update service request details</p>
</x-slot>

<x-card>
    <form action="{{ route('service-requests.update', $serviceRequest) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <x-select
                name="type"
                label="Service Type"
                :options="[
                    'cleaning' => 'Cleaning',
                    'setup' => 'Setup',
                    'technical_support' => 'Technical Support',
                    'internet' => 'Internet',
                    'electrical' => 'Electrical'
                ]"
                required
                :value="old('type', $serviceRequest->type)"
                :error="$errors->first('type')"
            />
            
            <x-input
                type="text"
                name="title"
                label="Title"
                placeholder="e.g., Clean Room 301"
                required
                :value="old('title', $serviceRequest->title)"
                :error="$errors->first('title')"
            />
            
            <x-textarea
                name="description"
                label="Description"
                placeholder="Describe the service needed..."
                rows="4"
                required
                :value="old('description', $serviceRequest->description)"
                :error="$errors->first('description')"
            />
            
            <x-input
                type="text"
                name="location"
                label="Location (Optional)"
                placeholder="e.g., Building A, Room 301"
                :value="old('location', $serviceRequest->location)"
                :error="$errors->first('location')"
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
                :value="old('priority', $serviceRequest->priority)"
                :error="$errors->first('priority')"
            />
            
            <x-select
                name="assigned_to"
                label="Assign To (Optional)"
                :options="['' => 'Select staff'] + $staff->pluck('name', 'id')->toArray()"
                :value="old('assigned_to', $serviceRequest->assigned_to)"
                :error="$errors->first('assigned_to')"
            />
            
            <x-input
                type="date"
                name="scheduled_date"
                label="Scheduled Date (Optional)"
                :value="old('scheduled_date', $serviceRequest->scheduled_date?->format('Y-m-d'))"
                :error="$errors->first('scheduled_date')"
            />
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('service-requests.show', $serviceRequest) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Service Request
            </button>
        </div>
    </form>
</x-card>
