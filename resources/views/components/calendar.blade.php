@props([
    'heatmap',
    'interactive' => false,
    'label' => null,
])

@php
    use Awcodes\Thermal\Support\HeatmapColor;

    /** @var \Awcodes\Thermal\Calendar\CalendarHeatmap $heatmap */
    $monthLabels = $heatmap->getMonthLabels();
    $cells = $heatmap->getCells();
    // Without a selection, the tab stop is the most recent day, which is where the calendar is scrolled to.
    $focusKey = collect($cells)->first(fn ($cell) => $cell->isSelected())?->getKey() ?? array_key_last($cells);
@endphp

<div
    {{
        $attributes
            ->merge(['role' => 'group', 'aria-label' => $label])
            ->class(['fi-thermal-calendar grid w-fit max-w-full gap-2'])
            ->style([
                HeatmapColor::getCssVariables($heatmap->getColor(), alias: 'thermal::calendar'),
            ])
    }}
>
    {{--
        Narrow containers scroll horizontally, starting at the most recent week. The padding,
        offset by a negative margin, keeps focus and selection outlines on edge cells from being clipped.
    --}}
    <div x-init="$el.scrollLeft = $el.scrollWidth" class="-m-1 overflow-x-auto p-1">
        <x-thermal::grid
            :interactive="$interactive"
            class="grid justify-start gap-0.75 text-xs text-gray-500 dark:text-gray-400"
            style="grid-template-columns: auto repeat({{ $heatmap->getWeeks() }}, minmax(0.625rem, 1.125rem)); min-width: calc({{ $heatmap->getWeeks() }} * 0.8125rem + 2rem);"
        >
            @foreach ($monthLabels as $week => $monthLabel)
                <span
                    aria-hidden="true"
                    class="truncate leading-4"
                    style="grid-row: 1; grid-column: {{ $week + 2 }} / span 3;"
                >{{ $monthLabel }}</span>
            @endforeach

            {{-- Keeps scrolled month labels from showing beside the sticky weekday labels. --}}
            <span
                aria-hidden="true"
                class="sticky start-0 z-10 bg-white shadow-[3px_0_0_0_var(--color-white)] dark:bg-gray-900 dark:shadow-[3px_0_0_0_var(--color-gray-900)]"
                style="grid-row: 1; grid-column: 1;"
            ></span>

            @foreach ($heatmap->getWeekdayLabels() as $day => $weekdayLabel)
                <span
                    aria-hidden="true"
                    class="sticky start-0 z-10 flex items-center bg-white pe-1 leading-none shadow-[3px_0_0_0_var(--color-white)] dark:bg-gray-900 dark:shadow-[3px_0_0_0_var(--color-gray-900)]"
                    style="grid-row: {{ $day + 2 }}; grid-column: 1;"
                >{{ $weekdayLabel }}</span>
            @endforeach

            @foreach ($heatmap->getColumns() as $week => $days)
                @foreach ($days as $day => $cell)
                    @if ($cell)
                        <x-thermal::cell
                            :cell="$cell"
                            :interactive="$interactive"
                            :row="$day + 1"
                            :column="$week + 1"
                            :focusable="$cell->getKey() === $focusKey"
                            :wire:click="$interactive ? 'selectCell(' . \Illuminate\Support\Js::from($cell->getKey()) . ')' : null"
                            class="aspect-square rounded-xs"
                            style="grid-row: {{ $day + 2 }}; grid-column: {{ $week + 2 }};"
                        />
                    @endif
                @endforeach
            @endforeach
        </x-thermal::grid>
    </div>

    @if ($heatmap->hasLegend())
        <x-thermal::legend :less="$heatmap->getLegendLessLabel()" :more="$heatmap->getLegendMoreLabel()" />
    @endif
</div>
