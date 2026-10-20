@props([
    'heatmap',
    'interactive' => false,
])

@php
    use function Filament\Support\get_color_css_variables;

    /** @var \Awcodes\Thermal\Calendar\CalendarHeatmap $heatmap */
    $monthLabels = $heatmap->getMonthLabels();
@endphp

<div
    {{
        $attributes
            ->class(['fi-thermal-calendar grid w-fit max-w-full gap-2'])
            ->style([
                get_color_css_variables($heatmap->getColor(), shades: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950], alias: 'thermal::calendar'),
            ])
    }}
>
    {{-- Narrow containers scroll horizontally, starting at the most recent week. --}}
    <div x-init="$el.scrollLeft = $el.scrollWidth" class="overflow-x-auto">
        <div
            class="grid justify-start gap-0.75 text-xs text-gray-500 dark:text-gray-400"
            style="grid-template-columns: auto repeat({{ $heatmap->getWeeks() }}, minmax(0.625rem, 1.125rem)); min-width: calc({{ $heatmap->getWeeks() }} * 0.8125rem + 2rem);"
        >
            @foreach ($monthLabels as $week => $label)
                <span
                    aria-hidden="true"
                    class="truncate leading-4"
                    style="grid-row: 1; grid-column: {{ $week + 2 }} / span 3;"
                >{{ $label }}</span>
            @endforeach

            {{-- Keeps scrolled month labels from showing beside the sticky weekday labels. --}}
            <span
                aria-hidden="true"
                class="sticky start-0 z-10 bg-white shadow-[3px_0_0_0_var(--color-white)] dark:bg-gray-900 dark:shadow-[3px_0_0_0_var(--color-gray-900)]"
                style="grid-row: 1; grid-column: 1;"
            ></span>

            @foreach ($heatmap->getWeekdayLabels() as $day => $label)
                <span
                    aria-hidden="true"
                    class="sticky start-0 z-10 flex items-center bg-white pe-1 leading-none shadow-[3px_0_0_0_var(--color-white)] dark:bg-gray-900 dark:shadow-[3px_0_0_0_var(--color-gray-900)]"
                    style="grid-row: {{ $day + 2 }}; grid-column: 1;"
                >{{ $label }}</span>
            @endforeach

            @foreach ($heatmap->getColumns() as $week => $days)
                @foreach ($days as $day => $cell)
                    @if ($cell)
                        <x-thermal::cell
                            :cell="$cell"
                            :interactive="$interactive"
                            :wire:click="$interactive ? 'selectCell(' . \Illuminate\Support\Js::from($cell->getKey()) . ')' : null"
                            class="aspect-square rounded-xs"
                            style="grid-row: {{ $day + 2 }}; grid-column: {{ $week + 2 }};"
                        />
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>

    @if ($heatmap->hasLegend())
        <x-thermal::legend :less="$heatmap->getLegendLessLabel()" :more="$heatmap->getLegendMoreLabel()" />
    @endif
</div>
