<div class="space-y-4">
    @forelse($reports as $issue)
        @php($issueType = \App\Models\BorrowerIssueReport::KINDS[$issue->kind])
        <article class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900/40">
            <header class="rounded-t-xl border-b border-gray-200 bg-gray-50 px-4 py-4 dark:border-gray-700 dark:bg-gray-900/40 sm:px-5">
                <span @class([
                    'inline-block rounded-full px-2.5 py-1 text-xs font-semibold',
                    'bg-amber-100 text-amber-900 dark:bg-amber-900/40 dark:text-amber-200' => $issue->kind === 'damage',
                    'bg-purple-100 text-purple-900 dark:bg-purple-900/40 dark:text-purple-200' => $issue->kind === 'missing_item',
                    'bg-blue-100 text-blue-900 dark:bg-blue-900/40 dark:text-blue-200' => $issue->kind === 'facility_problem',
                ])>{{ $issueType }}</span>
                <h2 class="mt-2 break-words text-lg font-semibold text-gray-900 dark:text-white">{{ $issue->location }}</h2>
                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600 dark:text-gray-300">
                    @if($reviewing && $issue->user)
                        <span class="inline-flex min-w-0 items-center gap-1.5">
                            <x-heroicon-o-user class="h-4 w-4 shrink-0" aria-hidden="true" />
                            <span class="min-w-0 break-words">{{ $issue->user->name }}</span>
                        </span>
                    @endif
                    <time datetime="{{ $issue->created_at->toAtomString() }}" class="inline-flex items-center gap-1.5">
                        <x-heroicon-o-clock class="h-4 w-4 shrink-0" aria-hidden="true" />
                        {{ $issue->created_at->format('M j, Y g:i A') }}
                    </time>
                </div>
            </header>
            <p class="whitespace-pre-wrap break-words px-4 py-4 text-sm leading-relaxed text-gray-700 dark:text-gray-200 sm:px-5">{{ $issue->description }}</p>
            @if($issue->photos)
                <ul class="flex flex-wrap gap-2 border-t border-gray-200 px-4 py-3 dark:border-gray-700 sm:px-5">
                    @foreach($issue->photos as $index => $path)
                        <li>
                            <a href="{{ route('borrower.issues.photo', [$issue, $index]) }}"
                               data-photo-title="Report photo {{ $loop->iteration }}"
                               x-on:click.prevent="photoUrl = $el.href; photoTitle = $el.dataset.photoTitle; $refs.photoDialog.showModal()"
                               class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-blue-700 hover:border-blue-300 hover:bg-blue-50 focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-700 dark:text-blue-300 dark:hover:border-blue-500 dark:hover:bg-blue-900/30">
                                <x-heroicon-o-paperclip class="h-4 w-4" aria-hidden="true" />
                                View photo {{ $loop->iteration }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>
    @empty
        <div class="rounded-xl border border-dashed border-gray-300 px-4 py-10 text-center dark:border-gray-600">
            <x-heroicon-o-clipboard-document-list class="mx-auto mb-3 h-8 w-8 text-gray-400" aria-hidden="true" />
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300"> {{ $issueTypeFilter ? 'No reports match this issue type.' : 'No issue reports yet.' }}</p>
        </div>
    @endforelse
</div>
@if($reports->hasPages())
    <div class="mt-5" data-report-pagination>{{ $reports->links() }}</div>
@endif
