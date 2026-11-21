<x-app-layout>
    <x-slot name="title">{{ $reviewing ? 'Issue Reports' : 'Report an Issue' }}</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $reviewing ? 'Issue Reports' : 'Report an Issue' }}</h1>
    </x-slot>

    <div class="space-y-6">
        @unless($reviewing)
            <x-card>
                <form method="POST" action="{{ route('borrower.issues.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-select name="kind" label="Issue type" :options="\App\Models\BorrowerIssueReport::KINDS" :error="$errors->first('kind')" required />
                        <x-input name="location" label="Item or location" placeholder="Equipment, room, or facility name" maxlength="255" :error="$errors->first('location')" required />
                    </div>
                    <x-textarea name="description" label="What happened?" rows="5" maxlength="5000" :error="$errors->first('description')" required />
                    <div>
                        <label for="issue-photos" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Photos (optional)</label>
                        <input id="issue-photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple aria-describedby="issue-photo-help"
                               class="block w-full text-sm text-gray-700 dark:text-gray-300">
                        <p id="issue-photo-help" class="mt-1 text-xs text-gray-500 dark:text-gray-400">Up to three JPG, PNG, or WebP photos, 5 MB each.</p>
                        @error('photos')
                            <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                        @enderror
                        @foreach($errors->get('photos.*') as $messages)
                            @foreach($messages as $message)
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
                            @endforeach
                        @endforeach
                    </div>
                    <x-button type="submit">Submit Report</x-button>
                </form>
            </x-card>
        @endunless

        <x-card :title="$reviewing ? 'Submitted Reports' : 'Your Reports'">
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($reports as $issue)
                    <article class="py-4">
                        <h2 class="font-semibold text-gray-900 dark:text-white">{{ \App\Models\BorrowerIssueReport::KINDS[$issue->kind] }} · {{ $issue->location }}</h2>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $issue->created_at->format('M j, Y g:i A') }}
                            @if($reviewing && $issue->user) · {{ $issue->user->name }} @endif
                        </p>
                        <p class="mt-2 whitespace-pre-wrap break-words text-sm text-gray-700 dark:text-gray-200">{{ $issue->description }}</p>
                        @if($issue->photos)
                            <ul class="mt-2 flex flex-wrap gap-3">
                                @foreach($issue->photos as $index => $path)
                                    <li><a href="{{ route('borrower.issues.photo', [$issue, $index]) }}" class="text-sm font-medium text-blue-700 hover:underline dark:text-blue-300">View photo {{ $loop->iteration }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                @empty
                    <p class="py-6 text-sm text-gray-500 dark:text-gray-400">No issue reports yet.</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $reports->links() }}</div>
        </x-card>
    </div>
</x-app-layout>
