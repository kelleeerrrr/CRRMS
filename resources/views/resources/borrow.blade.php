<x-app-layout>
    <x-slot name="title">Borrow {{ $resource->name }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Borrow {{ $resource->name }}</h1>
            <a href="{{ route('resources.index') }}" class="text-sm font-medium text-blue-700 hover:underline dark:text-blue-300">Back to Other Resources</a>
        </div>
    </x-slot>

    <x-card class="mx-auto max-w-3xl">
        <x-input-error :messages="$errors->all()" class="mb-4" />
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Request to Borrow</h3>

            <!-- 1. Resource Details -->
            <div class="mb-6 p-4 bg-white dark:bg-gray-700 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">1. Resource Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Resource Name:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">{{ $resource->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Category:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">{{ $resource->category }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Owner:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">{{ $resource->owner ? $resource->owner->name : 'School' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Condition:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">{{ ucfirst($resource->condition) }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-gray-500 dark:text-gray-400">Available Quantity:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">{{ $resource->available_quantity }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('resource-borrow-requests.store') }}">
                @csrf
                <input type="hidden" name="resource_id" value="{{ $resource->id }}">

                <!-- 2. Borrower Information -->
                <div class="mb-6 p-4 bg-white dark:bg-gray-700 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">2. Borrower Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Full Name*</label>
                            <input type="text" name="borrower_name" required value="{{ auth()->user()->name }}" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white bg-gray-100">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Contact Number*</label>
                            <input type="text" name="contact_number" required value="{{ auth()->user()->phone_number ?? '' }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                            <input type="email" name="email" value="{{ auth()->user()->email }}" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white bg-gray-100">
                        </div>
                    </div>
                </div>

                <!-- 3. Borrowing Details -->
                <div class="mb-6 p-4 bg-white dark:bg-gray-700 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">3. Borrowing Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Quantity to Borrow*</label>
                            <input type="number" name="quantity" required min="1" max="{{ $resource->available_quantity }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cannot exceed available quantity ({{ $resource->available_quantity }})</p>
                        </div>
                        <div></div>
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Borrow Date and Time*</label>
                            <input type="datetime-local" name="start_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Expected Return Date and Time*</label>
                            <input type="datetime-local" name="end_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Purpose of Borrowing*</label>
                            <textarea name="purpose" required rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Why do you need this resource?"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Intended Use or Location (optional)</label>
                            <input type="text" name="intended_use" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Where will you use this resource?">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Additional Notes (optional)</label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Any additional information..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- 4. Agreement -->
                <div class="mb-4 p-4 bg-white dark:bg-gray-700 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">4. Agreement</h4>
                    <label class="flex items-start">
                        <input type="checkbox" name="agreement" required class="w-4 h-4 mt-1 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                            I agree to take proper care of the resource and return it by the approved due date. I understand that I am responsible for any loss or damage according to the applicable borrowing rules.
                        </span>
                    </label>
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Submit Request
                </button>
            </form>
        </div>
    </x-card>
</x-app-layout>
