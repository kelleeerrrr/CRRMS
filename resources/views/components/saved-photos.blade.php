@props(['paths'])

@if($paths)
    @php($disk = \Illuminate\Support\Facades\Storage::disk('public'))
    <div class="mb-3">
        <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ count($paths) === 1 ? 'Saved photo' : 'Saved photos' }}</p>
        <div class="flex flex-wrap gap-3">
            @foreach($paths as $path)
                @if($disk->exists($path))
                    <a href="{{ asset('storage/' . $path) }}" target="_blank" rel="noopener" aria-label="Open saved photo {{ $loop->iteration }}">
                        <img src="{{ asset('storage/' . $path) }}" alt="Saved photo {{ $loop->iteration }}" class="h-24 w-24 rounded-lg border border-gray-300 object-cover dark:border-gray-600">
                    </a>
                @else
                    <p class="text-sm text-gray-600 dark:text-gray-400">Saved photo file is unavailable.</p>
                @endif
            @endforeach
        </div>
    </div>
@endif
