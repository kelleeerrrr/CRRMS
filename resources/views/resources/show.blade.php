<x-app-layout>
    <x-slot name="title">{{ $resource->name }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $resource->name }}</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Resource details</p>
            </div>
            <a href="{{ route('resources.index') }}" class="text-sm font-medium text-blue-700 hover:underline dark:text-blue-300">Back to Other Resources</a>
        </div>
    </x-slot>

    <x-card class="mx-auto max-w-4xl">
        <div class="space-y-6">
            <x-status-badge :status="$resource->status" />
            @include('resources.details')
        </div>
        <div class="mt-6">
            @if(auth()->id() === $resource->owner_id)
                <div class="flex space-x-2">
                    <a href="{{ route('resources.edit', $resource) }}" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-center">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('resources.destroy', $resource) }}" onsubmit="return confirm('Are you sure?')" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            @elseif(auth()->user()->roles()->first()?->name === 'user' && $resource->canBeBorrowedBy(auth()->user()))
                <a href="{{ route('resources.borrow', $resource) }}" class="inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Borrow</a>
            @endif
        </div>
    </x-card>
</x-app-layout>
