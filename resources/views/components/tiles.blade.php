@props([
    'heatmap',
    'interactive' => false,
])

@php
    use function Filament\Support\get_color_css_variables;

    /** @var \Awcodes\Thermal\Tiles\TileHeatmap $heatmap */
    $layout = $heatmap->getLayout();
    $columns = $layout->getColumns();
    $rows = $layout->getRows();
@endphp

<div
    {{
        $attributes
            ->class(['fi-thermal-tiles'])
            ->style([
                'container-type: inline-size',
                get_color_css_variables($heatmap->getColor(), shades: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950], alias: 'thermal::tiles'),
            ])
    }}
>
    <div class="mx-auto grid w-fit max-w-full gap-2">
        {{-- Tiles shrink with the container down to a minimum size, then scroll, so the geometry never wraps. --}}
        <div class="overflow-x-auto">
            <div
                class="grid w-fit gap-1"
                style="--thermal-tile-size: clamp(2rem, calc((100cqi - {{ max(0, $columns - 1) }} * 0.25rem) / {{ max(1, $columns) }}), 3.5rem); grid-template-columns: repeat({{ $columns }}, var(--thermal-tile-size)); grid-template-rows: repeat({{ $rows }}, var(--thermal-tile-size)); font-size: clamp(0.625rem, calc(var(--thermal-tile-size) * 0.3), 0.875rem);"
            >
                @foreach ($layout->getTiles() as $key => $tile)
                    @php
                        $cell = $heatmap->getCell((string) $key);
                    @endphp

                    <x-thermal::cell
                        :cell="$cell"
                        :interactive="$interactive"
                        :wire:click="$interactive ? 'selectCell(' . \Illuminate\Support\Js::from($cell->getKey()) . ')' : null"
                        class="flex items-center justify-center overflow-hidden rounded-md leading-none font-medium"
                        style="grid-row: {{ $tile->getRow() }}; grid-column: {{ $tile->getColumn() }};"
                    >
                        <span aria-hidden="true" class="truncate px-0.5">{{ $tile->getLabel() }}</span>
                    </x-thermal::cell>
                @endforeach
            </div>
        </div>

        @if ($heatmap->hasLegend())
            <x-thermal::legend :less="$heatmap->getLegendLessLabel()" :more="$heatmap->getLegendMoreLabel()" />
        @endif
    </div>
</div>
