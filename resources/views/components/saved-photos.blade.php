@props(['paths', 'label' => null, 'preview' => false])

@if($paths)
    @php($disk = \Illuminate\Support\Facades\Storage::disk('public'))
    <div class="mb-3" @if($preview) x-data="{ photoUrl: null, photoLabel: '' }" @endif>
        <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label ?? (count($paths) === 1 ? 'Saved photo' : 'Saved photos') }}</p>
        <div class="flex flex-wrap gap-3">
            @foreach($paths as $path)
                @if($disk->exists($path))
                    <a href="{{ asset('storage/' . $path) }}" aria-label="Open photo {{ $loop->iteration }}"
                       @if($preview)
                           aria-haspopup="dialog"
                           data-photo-label="Photo {{ $loop->iteration }}"
                           x-on:click.prevent="photoUrl = $el.href; photoLabel = $el.dataset.photoLabel; $refs.photoPreview.showModal()"
                       @else
                           target="_blank" rel="noopener"
                       @endif
                       class="rounded-lg focus-visible:ring-2 focus-visible:ring-blue-500">
                        <img loading="lazy" src="{{ asset('storage/' . $path) }}" alt="Photo {{ $loop->iteration }}" class="h-24 w-24 rounded-lg border border-gray-300 object-cover dark:border-gray-600">
                    </a>
                @else
                    <p class="text-sm text-gray-600 dark:text-gray-400">Saved photo file is unavailable.</p>
                @endif
            @endforeach
        </div>

        @if($preview)
            <dialog x-ref="photoPreview" x-id="['inventory-photo-title']" x-bind:aria-labelledby="$id('inventory-photo-title')"
                    x-on:close="photoUrl = null"
                    class="m-auto w-[calc(100%-2rem)] max-w-4xl rounded-xl bg-white text-gray-900 shadow-xl backdrop:bg-black/70 dark:bg-gray-800 dark:text-white">
                <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <h2 x-bind:id="$id('inventory-photo-title')" x-text="photoLabel" class="font-semibold"></h2>
                    <button type="button" autofocus aria-label="Close photo preview" x-on:click="$refs.photoPreview.close()"
                            class="rounded-lg p-2 hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-gray-700">
                        <x-heroicon-o-x-mark class="h-5 w-5" />
                    </button>
                </div>
                <div class="p-4">
                    <img x-bind:src="photoUrl" x-bind:alt="photoLabel" class="mx-auto max-h-[70dvh] max-w-full object-contain">
                </div>
            </dialog>
        @endif
    </div>
@endif
