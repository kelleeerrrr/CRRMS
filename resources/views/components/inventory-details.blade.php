@props(['title', 'status', 'sections' => [], 'description' => null, 'photos' => []])

<div x-data>
    <button type="button" aria-haspopup="dialog" x-on:click="$refs.details.showModal()"
            class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
        View Details
    </button>

    <dialog x-ref="details" x-id="['inventory-detail-title']" x-bind:aria-labelledby="$id('inventory-detail-title')"
            class="m-auto max-h-[85dvh] w-[calc(100%-2rem)] max-w-2xl overscroll-contain rounded-xl bg-white text-gray-900 shadow-xl backdrop:bg-black/60 dark:bg-gray-800 dark:text-white">
        <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 sm:px-6">
            <div class="min-w-0">
                <h2 x-bind:id="$id('inventory-detail-title')" class="break-words text-lg font-semibold">{{ $title }}</h2>
                <div class="mt-1"><x-status-badge :status="$status" /></div>
            </div>
            <form method="dialog">
                <button type="submit" autofocus aria-label="Close details"
                        class="rounded-lg p-2 hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-gray-700">
                    <x-heroicon-o-x-mark class="h-5 w-5" />
                </button>
            </form>
        </div>
        <div class="space-y-6 p-4 sm:p-6">
            @if($slot->isNotEmpty())
                {{ $slot }}
            @else
                <x-inventory-detail-content :sections="$sections" :description="$description" :photos="$photos" />
            @endif
        </div>
    </dialog>
</div>
