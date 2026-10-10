@props(['breadcrumbs' => []])

<nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2 text-sm">
        <li>
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <x-heroicon-o-home class="w-4 h-4" />
            </a>
        </li>
        @foreach($breadcrumbs as $index => $breadcrumb)
            <li class="flex items-center">
                <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-400 mx-2" />
                @if($index === count($breadcrumbs) - 1)
                    <span class="font-medium text-gray-900 dark:text-white">{{ $breadcrumb['title'] }}</span>
                @else
                    <a href="{{ $breadcrumb['url'] ?? '#' }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ $breadcrumb['title'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
