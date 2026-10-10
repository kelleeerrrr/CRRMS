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
        const options = {
            series: [{
                name: 'Count',
                data: @js($data)
            }],
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
            colors: ['#3B82F6'],
            xaxis: {
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
