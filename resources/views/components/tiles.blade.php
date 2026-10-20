@props([
    'heatmap',
    'interactive' => false,
    'label' => null,
])

@php
    use Awcodes\Thermal\Support\HeatmapColor;

    /** @var \Awcodes\Thermal\Tiles\TileHeatmap $heatmap */
    $layout = $heatmap->getLayout();
    $columns = $layout->getColumns();
    $rows = $layout->getRows();
    $cells = $heatmap->getCells();
    $focusKey = collect($cells)->first(fn ($cell) => $cell->isSelected())?->getKey() ?? array_key_first($cells);
@endphp

<div
    {{
        $attributes
            ->merge(['role' => 'group', 'aria-label' => $label])
            ->class(['fi-thermal-tiles'])
            ->style([
                'container-type: inline-size',
                HeatmapColor::getCssVariables($heatmap->getColor(), alias: 'thermal::tiles'),
            ])
    }}
>
    <div class="mx-auto grid w-fit max-w-full gap-2">
        {{--
            Tiles shrink with the container down to a minimum size, then scroll, so the geometry never wraps.
            The padding, offset by a negative margin, keeps focus and selection outlines on edge tiles from being clipped.
        --}}
        <div class="-m-1 overflow-x-auto p-1">
            <x-thermal::grid
                :interactive="$interactive"
                class="grid w-fit gap-1"
                style="--thermal-tile-size: clamp(2rem, calc((100cqi - {{ max(0, $columns - 1) }} * 0.25rem) / {{ max(1, $columns) }}), 3.5rem); grid-template-columns: repeat({{ $columns }}, var(--thermal-tile-size)); grid-template-rows: repeat({{ $rows }}, var(--thermal-tile-size)); font-size: clamp(0.625rem, calc(var(--thermal-tile-size) * 0.3), 0.875rem);"
            >
                @foreach ($layout->getTiles() as $key => $tile)
                    @php
                        $cell = $cells[$key];
                    @endphp

                    <x-thermal::cell
                        :cell="$cell"
                        :interactive="$interactive"
                        :row="$tile->getRow()"
                        :column="$tile->getColumn()"
                        :focusable="$cell->getKey() === $focusKey"
                        :wire:click="$interactive ? 'selectCell(' . \Illuminate\Support\Js::from($cell->getKey()) . ')' : null"
                        class="flex items-center justify-center overflow-hidden rounded-md leading-none font-medium"
                        style="grid-row: {{ $tile->getRow() }}; grid-column: {{ $tile->getColumn() }};"
                    >
                        <span aria-hidden="true" class="truncate px-0.5">{{ $tile->getLabel() }}</span>
                    </x-thermal::cell>
                @endforeach
            </x-thermal::grid>
        </div>

        @if ($heatmap->hasLegend())
            <x-thermal::legend :less="$heatmap->getLegendLessLabel()" :more="$heatmap->getLegendMoreLabel()" />
        @endif
    </div>
</div>
