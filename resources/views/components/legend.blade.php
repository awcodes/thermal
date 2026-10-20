@props([
    'less' => null,
    'more' => null,
])

@php
    use Awcodes\Thermal\Support\HeatmapCell;
    use Awcodes\Thermal\Support\HeatmapScale;

    $less ??= __('thermal::heatmap.legend.less');
    $more ??= __('thermal::heatmap.legend.more');
@endphp

<div
    {{ $attributes->class(['fi-thermal-legend flex items-center justify-end gap-1 text-xs text-gray-500 dark:text-gray-400']) }}
>
    <span class="sr-only">{{ __('thermal::heatmap.legend.label', ['less' => $less, 'more' => $more]) }}</span>
    <span aria-hidden="true">{{ $less }}</span>

    @foreach (range(0, HeatmapScale::MAX_LEVEL) as $level)
        <x-thermal::cell
            :cell="HeatmapCell::make('legend-' . $level)->value($level)->level($level)"
            decorative
            class="size-3 rounded-sm"
        />
    @endforeach

    <span aria-hidden="true">{{ $more }}</span>
</div>
