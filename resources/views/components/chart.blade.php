@props([
    'type' => 'line',
    'data' => [],
    'options' => [],
    'height' => '300',
])

<div 
    x-data="{
        chart: null,
        initChart() {
            const options = {
                series: @js($data['series'] ?? []),
                chart: {
                    type: '{{ $type }}',
                    height: {{ $height }},
                    toolbar: { show: false },
                },
                xaxis: @js($data['xaxis'] ?? []),
                yaxis: @js($data['yaxis'] ?? []),
                colors: @js($data['colors'] ?? ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']),
                ...@js($options),
            };
            
            this.chart = new ApexCharts(this.$el, options);
            this.chart.render();
        },
        destroyChart() {
            if (this.chart) {
                this.chart.destroy();
            }
        }
    }"
    x-init="initChart()"
    x-on:livewire:update="destroyChart(); initChart()"
    x-on:livewire:updated="destroyChart(); initChart()"
    class="w-full"
></div>
