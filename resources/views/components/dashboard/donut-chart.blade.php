@props([
    'data' => [],
    'title' => null,
    'height' => 250,
    'loading' => false,
])

@php
    $chartId = 'donut-chart-' . ($attributes['id'] ?? uniqid());
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
            series: @js(array_column($data, 'y')),
            labels: @js(array_column($data, 'x')),
            chart: {
                type: 'donut',
                height: {{ $height }},
                toolbar: { show: false },
            },
            colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                            },
                            value: {
                                show: true,
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val, opts) {
                    return opts.w.globals.series[opts.seriesIndex]
                }
            },
            legend: {
                position: 'bottom',
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
