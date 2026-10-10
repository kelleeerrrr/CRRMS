@props([
    'columns' => [],
    'rows' => [],
    'empty' => 'No data available',
    'loading' => false,
])

<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800/50">
            <tr>
                @foreach($columns as $column)
                    <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        {{ $column['label'] }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @if($loading)
                <tr>
                    <td colspan="{{ count($columns) }}" class="px-6 py-12 text-center">
                        <div class="flex justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
                        </div>
                    </td>
                </tr>
            @elseif(empty($rows))
                <tr>
                    <td colspan="{{ count($columns) }}" class="px-6 py-12 text-center">
                        <div class="text-gray-500 dark:text-gray-400">
                            <x-heroicon-o-inbox class="w-8 h-8 mx-auto mb-4 opacity-50" />
                            <p class="text-sm">{{ $empty }}</p>
                        </div>
                    </td>
                </tr>
            @else
                @foreach($rows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-150">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap">
                                @isset($column['slot'])
                                    {{ $column['slot']($row) }}
                                @else
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $row[$column['key']] ?? '-' }}</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
