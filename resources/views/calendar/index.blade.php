@php
    $filters = $isOfficer
        ? ['all' => 'All'] + array_map(fn ($type) => $type['label'], $requestTypes)
        : ['all' => 'All', 'reservations' => 'Reservations', 'borrowing' => 'Borrowing', 'lending' => 'Lending'];
@endphp

<x-app-layout>
    <x-slot name="title">{{ $isOfficer ? 'School Calendar' : 'My Calendar' }}</x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $isOfficer ? 'School Calendar' : 'My Calendar' }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ $isOfficer ? 'See who booked or borrowed school facilities, equipment, and resources.' : 'View your reservations, borrowing, and lending activities.' }}
        </p>
    </x-slot>

    <x-card class="mb-6">
        <nav aria-label="Calendar filters" class="flex flex-wrap gap-2">
            @foreach($filters as $key => $label)
                <a href="{{ route('calendar.index', ['filter' => $key]) }}" @if($filter === $key) aria-current="page" @endif
                    class="rounded-lg px-4 py-2 text-sm font-medium transition-colors {{ $filter === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <ul aria-label="Calendar colors" class="mt-4 flex flex-wrap gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
            @foreach($requestTypes as $type)
                <li class="rounded-full px-3 py-1 text-xs font-medium text-white" style="background-color: {{ $type['color'] }}">{{ $type['label'] }}</li>
            @endforeach
            @unless($isOfficer)
                <li class="rounded-full bg-amber-800 px-3 py-1 text-xs font-medium text-white">Pending</li>
            @endunless
        </ul>
    </x-card>

    <x-card>
        <p id="calendar-error" role="status" class="mb-4 text-sm text-red-700 dark:text-red-300" hidden>Calendar requests couldn’t load. Try refreshing the page.</p>
        <div id="calendar"></div>
    </x-card>

    <dialog id="eventModal" aria-labelledby="modalTitle" class="m-auto w-[calc(100%-2rem)] max-w-md rounded-xl bg-white p-6 text-gray-900 shadow-xl backdrop:bg-black/50 dark:bg-gray-800 dark:text-white">
        <form method="dialog" class="mb-4 flex items-start justify-between gap-4">
            <h2 id="modalTitle" class="text-lg font-semibold"></h2>
            <button class="rounded-md px-2 py-1 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">Close</button>
        </form>
        <dl id="modalContent" class="space-y-2 text-sm"></dl>
        <a id="event-details-link" class="mt-4 inline-flex rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">View request details</a>
    </dialog>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('calendar');
                const errorMessage = document.getElementById('calendar-error');
                const modal = document.getElementById('eventModal');
                const filter = @js($filter);
                const dataUrl = @js(route('calendar.data'));

                if (!window.FullCalendar) {
                    errorMessage.hidden = false;
                    return;
                }

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek',
                    },
                    dayMaxEvents: 3,
                    eventDisplay: 'block',
                    eventClassNames: ['!rounded-full'],
                    displayEventTime: false,
                    events(info, successCallback, failureCallback) {
                        const url = new URL(dataUrl);
                        url.search = new URLSearchParams({
                            start: info.startStr.slice(0, 10),
                            end: info.endStr.slice(0, 10),
                            filter,
                        });
                        fetch(url, { headers: { Accept: 'application/json' } })
                            .then(response => {
                                if (!response.ok || response.redirected) throw new Error('Unable to load calendar requests');
                                return response.json();
                            })
                            .then(events => {
                                errorMessage.hidden = true;
                                successCallback(events);
                            })
                            .catch(error => {
                                errorMessage.hidden = false;
                                failureCallback(error);
                            });
                    },
                    eventDidMount({ el, event }) {
                        el.title = event.title;
                    },
                    eventClick({ event }) {
                        const props = event.extendedProps;
                        const content = document.getElementById('modalContent');
                        const fields = [
                            ['Request type', props.type],
                            ['Requester', props.requester],
                            ['Item', props.item],
                            ['Scheduled for', props.period],
                            ['Status', props.status.replaceAll('_', ' ')],
                            ['Notes', props.description],
                        ];

                        document.getElementById('modalTitle').textContent = event.title;
                        document.getElementById('event-details-link').href = props.detailsUrl;
                        content.replaceChildren();
                        for (const [label, value] of fields) {
                            if (!value) continue;
                            const row = document.createElement('div');
                            const term = document.createElement('dt');
                            const description = document.createElement('dd');
                            term.className = 'font-semibold';
                            description.className = 'break-words text-gray-600 dark:text-gray-300';
                            term.textContent = label;
                            description.textContent = value;
                            row.append(term, description);
                            content.append(row);
                        }

                        modal.showModal();
                    },
                });

                calendar.render();
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) calendar.refetchEvents();
                });
                window.setInterval(() => {
                    if (!document.hidden) calendar.refetchEvents();
                }, 30000);
            });
        </script>
    @endpush
</x-app-layout>
