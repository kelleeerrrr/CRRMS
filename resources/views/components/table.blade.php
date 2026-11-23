@props([
    'columns' => [],
    'rows' => [],
    'empty' => 'No data available',
    'loading' => false,
])

<div class="overflow-x-auto rounded-xl shadow-sm" style="border: 1px solid var(--card-border);">
    <table class="min-w-full">
        <thead style="background: var(--card-header-bg);">
            <tr>
                @foreach($columns as $column)
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-title);">
                        {{ $column['label'] }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody style="background: var(--input-bg);">
            @if($loading)
                <tr>
                    <td colspan="{{ count($columns) }}" class="px-6 py-12 text-center">
                        <div class="flex justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2" style="border-color: var(--brand-maroon);"></div>
                        </div>
                    </td>
                </tr>
            @elseif(empty($rows))
                <tr>
                    <td colspan="{{ count($columns) }}" class="px-6 py-12 text-center">
                        <div style="color: var(--text-muted);">
                            <x-heroicon-o-inbox class="w-8 h-8 mx-auto mb-4 opacity-50" />
                            <p class="text-sm">{{ $empty }}</p>
                        </div>
                    </td>
                </tr>
            @else
                @foreach($rows as $index => $row)
                    <tr class="transition-colors duration-150" style="border-bottom: 1px solid var(--card-border); {{ $index % 2 === 0 ? 'background: var(--input-bg);' : 'background: var(--card-bg);' }}" @mouseover="this.style.background='var(--card-bg)'" @mouseout="this.style.background='{{ $index % 2 === 0 ? 'var(--input-bg)' : 'var(--card-bg)' }}'">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap">
                                @isset($column['slot'])
                                    {{ $column['slot']($row) }}
                                @else
                                    <span class="text-sm" style="color: var(--text-primary);">{{ $row[$column['key']] ?? '-' }}</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
