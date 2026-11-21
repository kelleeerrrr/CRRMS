<x-app-layout>
    <x-slot name="title">Resource Requests</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Resource Requests</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage resource borrowing requests</p>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form data-inventory-search method="GET" action="{{ route('resource-requests.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    type="search"
                    name="search"
                    :value="$search"
                    placeholder="Search by borrower or resource..."
                    label="Search"
                />
                
                <x-select
                    name="status"
                    placeholder=""
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
            
            <div class="mt-4 flex min-h-9 items-center gap-3">
                <span data-inventory-search-status role="status" class="mr-auto text-sm font-medium text-blue-700 dark:text-blue-300"></span>
                <noscript><x-button type="submit">Search</x-button></noscript>
                <a class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" href="{{ route('resource-requests.index') }}">Reset</a>
            </div>
        </form>
    </x-card>

    <div data-inventory-results aria-live="polite" aria-busy="false">
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
                                        <div class="whitespace-nowrap">
                                            <a href="{{ route('resource-borrow-requests.show', $request) }}" class="font-semibold text-blue-700 hover:underline dark:text-blue-400">View details</a>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $request->created_at->format('M d, Y H:i') }}</p>
                                        </div>
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
                                            @if($request->status === 'pending')
                                                <form method="POST" action="{{ route('resource-requests.approve', $request) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm">
                                                        Approve
                                                    </button>
                                                </form>
                                                <button onclick="showDeclineModal('{{ $request->id }}')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm ml-2">
                                                    Reject
                                                </button>
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
    </div>

    <!-- Decline Modal -->
    <div id="declineModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Decline Borrow Request</h3>
            <form method="POST" action="" id="declineForm">
                @csrf
                <input type="hidden" name="request_id" id="declineRequestId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Rejection (Optional)</label>
                    <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="Provide a reason for declining this request..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideDeclineModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Decline Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showDeclineModal(requestId) {
            document.getElementById('declineModal').classList.remove('hidden');
            document.getElementById('declineModal').classList.add('flex');
            document.getElementById('declineRequestId').value = requestId;
            document.getElementById('declineForm').action = '/resource-requests/' + requestId + '/decline';
        }

        function hideDeclineModal() {
            document.getElementById('declineModal').classList.add('hidden');
            document.getElementById('declineModal').classList.remove('flex');
        }
    </script>
</x-app-layout>
