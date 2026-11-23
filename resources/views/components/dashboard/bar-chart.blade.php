@props([
    'data' => [],
    'title' => null,
    'height' => 250,
    'loading' => false,
])

@php
    $chartId = 'bar-chart-' . ($attributes['id'] ?? uniqid());
@endphp

<div class="relative {{ $loading ? 'h-64' : '' }}">
    @if($loading)
        <x-loading-skeleton class="h-64" />
    @else
        <div class="flex items-center justify-between mb-4">
            @if($title)
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
            @endif
        </div>
        <div id="{{ $chartId }}" style="height: {{ $height }}px;"></div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const data = @js($data);

        // Check if data is multi-series format (has 'categories' and 'series')
        const isMultiSeries = data && data.categories && data.series;

        // Check if data is x/y format (array of objects with x and y keys)
        const isXYFormat = Array.isArray(data) && data.length > 0 && data[0].hasOwnProperty('x') && data[0].hasOwnProperty('y');

        let series, categories, colors;

        if (isMultiSeries) {
            series = data.series;
            categories = data.categories;
            colors = ['#3B82F6', '#10B981'];
        } else if (isXYFormat) {
            series = [{
                name: 'Count',
                data: data.map(item => item.y)
            }];
            categories = data.map(item => item.x);
            colors = ['#3B82F6'];
        } else {
            series = [{
                name: 'Count',
                data: data
            }];
            categories = undefined;
            colors = ['#3B82F6'];
        }

        const options = {
            series: series,
            chart: {
                type: 'bar',
                height: {{ $height }},
                toolbar: { show: false },
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%',
                }
            },
            colors: colors,
            xaxis: {
                categories: categories,
                labels: {
                    rotate: -45,
                },
            },
            yaxis: {
                labels: {
                    formatter: function (val) { return val.toFixed(0) }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) { return val }
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4,
            },
            theme: {
                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
            }
        };

        const chart = new ApexCharts(document.querySelector("#{{ $chartId }}"), options);
        chart.render();
    });
</script>
@endpush
