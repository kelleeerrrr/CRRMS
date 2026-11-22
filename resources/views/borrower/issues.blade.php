<x-app-layout>
    @php
        $pageTitle = $reviewing
            ? (auth()->user()->roles()->first()?->name === 'school_officer' ? 'Reports' : 'Issue Reports')
            : 'Report an Issue';
    @endphp
    <x-slot name="title">{{ $pageTitle }}</x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pageTitle }}</h1>
    </x-slot>

    <div class="space-y-6" x-data="{ photoUrl: null, photoTitle: '' }">
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
            <x-slot name="actions">
                <form method="GET" action="{{ route($reviewing ? 'borrower.issues.review' : 'borrower.issues.index') }}" class="ml-3" data-report-filter>
                    <label for="report-issue-type" class="sr-only">Filter reports by issue type</label>
                    <select id="report-issue-type" name="type" x-on:change="$el.form.requestSubmit()"
                            class="max-w-full rounded-lg border-gray-300 bg-white py-1.5 pl-2 pr-8 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:pl-3">
                        <option value="">All issue types</option>
                        @foreach(\App\Models\BorrowerIssueReport::KINDS as $kind => $label)
                            <option value="{{ $kind }}" @selected($issueTypeFilter === $kind)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="sr-only focus:not-sr-only">Apply filter</button>
                    <noscript><button type="submit" class="ml-2 text-sm font-medium">Apply</button></noscript>
                </form>
            </x-slot>

            <p class="text-sm text-gray-600 empty:hidden dark:text-gray-300" role="status" data-report-filter-status></p>
            <div class="aria-busy:opacity-60" data-report-results aria-busy="false">
                @include('borrower.issue-results')
            </div>
        </x-card>

        <dialog x-ref="photoDialog" aria-labelledby="issue-photo-title"
                class="m-auto w-[calc(100%-2rem)] max-w-3xl rounded-xl bg-white text-gray-900 shadow-xl backdrop:bg-black/60 dark:bg-gray-800 dark:text-white">
            <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h2 id="issue-photo-title" class="font-semibold" x-text="photoTitle"></h2>
                <form method="dialog">
                    <button type="submit" autofocus aria-label="Close photo preview"
                            class="rounded-lg p-2 hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-gray-700">
                        <x-heroicon-o-x-mark class="h-5 w-5" />
                    </button>
                </form>
            </div>
            <div class="p-4">
                <img x-bind:src="photoUrl" x-bind:alt="photoTitle" class="mx-auto max-h-[70vh] max-w-full object-contain">
            </div>
        </dialog>
    </div>
</x-app-layout>
