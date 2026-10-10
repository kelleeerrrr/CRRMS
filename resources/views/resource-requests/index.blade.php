<x-app-layout>
    <x-slot name="title">Resource Requests</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Resource Requests</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage resource borrowing requests</p>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('resource-requests.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    type="text"
                    name="search"
                    :value="$search"
                    placeholder="Search by borrower or resource..."
                    label="Search"
                />
                
                <x-select
                    name="status"
                    :value="$status"
                    label="Status"
                    :options="[
                        '' => 'All Status',
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'in_use' => 'In Use',
                        'returned' => 'Returned',
                        'cancelled' => 'Cancelled'
                    ]"
                />
            </div>
            
            <div class="mt-4 flex justify-end">
                <x-button variant="ghost" type="submit">Filter</x-button>
                <x-button variant="ghost" type="a" href="{{ route('resource-requests.index') }}">Reset</x-button>
            </div>
        </form>
    </x-card>

    <!-- Requests Table -->
    @if($requests->count() > 0)
        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Requested</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Borrower</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Resource</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Borrow Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Return Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-semibold mr-2">
                                            {{ strtoupper(substr($request->borrower_name ?? $request->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->borrower_name ?? $request->user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $request->email ?? $request->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->resource->name }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->quantity }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->start_date ? $request->start_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">
                                    {{ $request->end_date ? $request->end_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-3 px-4">
                                    <x-status-badge :status="$request->status" />
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                            View
                                        </a>
                                        @if($request->status === 'pending')
                                            <form method="POST" action="{{ route('resource-requests.approve', $request) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm">
                                                    Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('resource-requests.decline', $request) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        </x-card>
    @else
        <x-card>
            <div class="text-center py-12">
                <x-heroicon-o-document class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No resource requests found</h3>
                <p class="text-gray-600 dark:text-gray-400">There are no resource requests to display.</p>
            </div>
        </x-card>
    @endif
</x-app-layout>
