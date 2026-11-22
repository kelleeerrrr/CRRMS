@props(['sections', 'description' => null, 'photos' => []])

@if(filled($description))
    <p class="whitespace-pre-wrap break-words text-sm text-gray-700 dark:text-gray-300">{{ $description }}</p>
@endif
<x-saved-photos :paths="$photos" label="Photos" :preview="true" />

@foreach($sections as $heading => $fields)
    @php($visibleFields = collect($fields)->filter(fn ($value) => filled($value)))
    @if($visibleFields->isNotEmpty())
        <section class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
            <h3 class="mb-3 text-sm font-semibold">{{ $heading }}</h3>
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach($visibleFields as $label => $value)
                    <div class="min-w-0">
                        <dt class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $label }}</dt>
                        <dd class="mt-1 whitespace-pre-wrap break-words text-sm">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    @endif
@endforeach
