<x-app-layout>
    <x-slot name="title">Edit Resource Borrow Request</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Borrow Request</h1>
                <p class="text-gray-600 dark:text-gray-400">Request ID: {{ $request->id }}</p>
            </div>
            <a href="{{ route('resource-borrow-requests.show', $request) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
        </div>
    </x-slot>

    <x-card class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('resource-borrow-requests.update', $request) }}">
            @csrf
            @method('PUT')

            <!-- Resource Details (Read-only) -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Resource Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Resource Name:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">{{ $request->resource->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Category:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">{{ $request->resource->category ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Owner:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">{{ $request->resource->owner ? $request->resource->owner->name : 'School' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Condition:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">{{ ucfirst($request->resource->condition) }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-gray-500 dark:text-gray-400">Available Quantity:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">{{ $request->resource->available_quantity }}</span>
                    </div>
                </div>
            </div>

            <!-- Borrower Information -->
            <div class="mb-6 p-4 bg-white dark:bg-gray-700 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Borrower Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Full Name*</label>
                        <input type="text" name="borrower_name" required value="{{ $request->borrower_name }}" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Contact Number*</label>
                        <input type="text" name="contact_number" required value="{{ $request->contact_number }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ $request->email }}" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white bg-gray-100">
                    </div>
                </div>
            </div>

            <!-- Borrowing Details -->
            <div class="mb-6 p-4 bg-white dark:bg-gray-700 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Borrowing Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Quantity to Borrow*</label>
                        <input type="number" name="quantity" required min="1" max="{{ $request->resource->available_quantity }}" value="{{ $request->quantity }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cannot exceed available quantity ({{ $request->resource->available_quantity }})</p>
                    </div>
                    <div></div>
                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Borrow Date and Time*</label>
                        <input type="datetime-local" name="start_date" required value="{{ $request->start_date ? $request->start_date->format('Y-m-d\TH:i') : '' }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Expected Return Date and Time*</label>
                        <input type="datetime-local" name="end_date" required value="{{ $request->end_date ? $request->end_date->format('Y-m-d\TH:i') : '' }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Purpose of Borrowing*</label>
                        <textarea name="purpose" required rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Why do you need this resource?">{{ $request->purpose }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Intended Use or Location (optional)</label>
                        <input type="text" name="intended_use" value="{{ $request->intended_use ?? '' }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Where will you use this resource?">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Additional Notes (optional)</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Any additional information...">{{ $request->notes ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('resource-borrow-requests.show', $request) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update Request
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
