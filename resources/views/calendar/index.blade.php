<x-app-layout>
    <x-slot name="title">My Calendar</x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Calendar</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View your reservations, borrowing, and lending activities</p>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter:</span>
            <div class="flex space-x-2">
                <a href="{{ route('calendar.index', ['filter' => 'all']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    All
                </a>
                <a href="{{ route('calendar.index', ['filter' => 'reservations']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'reservations' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Reservations
                </a>
                <a href="{{ route('calendar.index', ['filter' => 'borrowing']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'borrowing' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Borrowing
                </a>
                <a href="{{ route('calendar.index', ['filter' => 'lending']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'lending' ? 'bg-pink-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Lending
                </a>
            </div>
        </div>
    </x-card>

    <!-- Calendar -->
    <x-card>
        <div id="calendar"></div>
    </x-card>

    <!-- Event Detail Modal -->
    <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 dark:text-white"></h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="modalContent" class="space-y-3"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                const filter = '{{ $filter }}';
                const startDate = '{{ $startDate }}';
                const endDate = '{{ $endDate }}';

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },
                    events: function(info, successCallback, failureCallback) {
                        fetch(`{{ route('calendar.data') }}?start=${info.startStr}&end=${info.endStr}&filter=${filter}`)
                            .then(response => response.json())
                            .then(data => successCallback(data))
                            .catch(error => failureCallback(error));
                    },
                    eventClick: function(info) {
                        const event = info.event;
                        const props = event.extendedProps;
                        
                        let content = '';
                        
                        if (props.type === 'facility_reservation') {
                            content = `
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Type:</span>
                                    <span class="ml-2">Facility Reservation</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Facility:</span>
                                    <span class="ml-2">${props.facility_name}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Date:</span>
                                    <span class="ml-2">${event.startStr.split('T')[0]}</span>
                                </div>
                                ${props.start_time ? `
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Time:</span>
                                    <span class="ml-2">${props.start_time} - ${props.end_time}</span>
                                </div>
                                ` : ''}
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Status:</span>
                                    <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold ${props.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${props.status}</span>
                                </div>
                                ${props.description ? `
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Description:</span>
                                    <p class="mt-1">${props.description}</p>
                                </div>
                                ` : ''}
                            `;
                        } else if (props.type === 'equipment_borrow') {
                            content = `
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Type:</span>
                                    <span class="ml-2">Equipment Borrowing</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Equipment:</span>
                                    <span class="ml-2">${props.equipment_names}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Date:</span>
                                    <span class="ml-2">${event.startStr} to ${event.endStr}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Status:</span>
                                    <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold ${props.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${props.status}</span>
                                </div>
                                ${props.purpose ? `
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Purpose:</span>
                                    <p class="mt-1">${props.purpose}</p>
                                </div>
                                ` : ''}
                            `;
                        } else if (props.type === 'resource_borrow') {
                            content = `
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Type:</span>
                                    <span class="ml-2">Resource Borrowing</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Resource:</span>
                                    <span class="ml-2">${props.resource_name}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Condition:</span>
                                    <span class="ml-2">${props.resource_condition}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Date:</span>
                                    <span class="ml-2">${event.startStr} to ${event.endStr}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Status:</span>
                                    <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold ${props.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${props.status}</span>
                                </div>
                                ${props.purpose ? `
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Purpose:</span>
                                    <p class="mt-1">${props.purpose}</p>
                                </div>
                                ` : ''}
                            `;
                        } else if (props.type === 'resource_lending') {
                            content = `
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Type:</span>
                                    <span class="ml-2">Resource Lending</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Resource:</span>
                                    <span class="ml-2">${props.resource_name}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Borrower:</span>
                                    <span class="ml-2">${props.borrower_name}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Date:</span>
                                    <span class="ml-2">${event.startStr} to ${event.endStr}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Status:</span>
                                    <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold ${props.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${props.status}</span>
                                </div>
                                ${props.purpose ? `
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Purpose:</span>
                                    <p class="mt-1">${props.purpose}</p>
                                </div>
                                ` : ''}
                            `;
                        }

                        document.getElementById('modalTitle').textContent = event.title;
                        document.getElementById('modalContent').innerHTML = content;
                        document.getElementById('eventModal').classList.remove('hidden');
                        document.getElementById('eventModal').classList.add('flex');
                    },
                    eventColor: function(info) {
                        return info.event.backgroundColor;
                    }
                });

                calendar.render();
            });

            function closeModal() {
                document.getElementById('eventModal').classList.add('hidden');
                document.getElementById('eventModal').classList.remove('flex');
            }

            // Close modal when clicking outside
            document.getElementById('eventModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        </script>
    @endpush
</x-app-layout>
