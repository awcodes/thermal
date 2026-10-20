@props([
    'cell',
    'interactive' => false,
    'decorative' => false,
    'row' => null,
    'column' => null,
    'focusable' => true,
])

@php
    /** @var \Awcodes\Thermal\Support\HeatmapCell $cell */
    $level = $cell->getLevel();
    $isMissing = ! $cell->hasValue();
    $interactive = $interactive && ! $decorative;
@endphp

<{{ $interactive ? 'button' : 'div' }}
    @if ($interactive)
        type="button"
        aria-pressed="{{ $cell->isSelected() ? 'true' : 'false' }}"
        tabindex="{{ $focusable ? 0 : -1 }}"
        @if ($row !== null && $column !== null)
            data-row="{{ $row }}"
            data-column="{{ $column }}"
        @endif
    @endif
    @if ($decorative)
        aria-hidden="true"
    @else
        data-key="{{ $cell->getKey() }}"
        data-level="{{ $level }}"
        @if ($isMissing)
            data-missing="true"
        @endif
        x-tooltip="{
            content: @js($cell->getTooltip()),
            theme: $store.theme,
        }"
    @endif
    {{
        $attributes->class([
            'fi-thermal-cell forced-color-adjust-none',
            'bg-transparent text-gray-500 ring-1 ring-gray-950/10 ring-inset dark:text-gray-400 dark:ring-white/15' => $isMissing,
            'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-300' => ! $isMissing && $level === 0,
            'bg-(--color-200) text-(--thermal-text-1) dark:bg-(--color-900) dark:text-(--thermal-dark-text-1)' => ! $isMissing && $level === 1,
            'bg-(--color-400) text-(--thermal-text-2) dark:bg-(--color-700) dark:text-(--thermal-dark-text-2)' => ! $isMissing && $level === 2,
            'bg-(--color-600) text-(--thermal-text-3) dark:bg-(--color-500) dark:text-(--thermal-dark-text-3)' => ! $isMissing && $level === 3,
            'bg-(--color-800) text-(--thermal-text-4) dark:bg-(--color-300) dark:text-(--thermal-dark-text-4)' => ! $isMissing && $level === 4,
            'cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 dark:focus-visible:outline-primary-500' => $interactive,
            'outline-2 outline-offset-1 outline-gray-950 dark:outline-white' => $cell->isSelected(),
        ])
    }}
>
    @unless ($decorative)
        <span class="sr-only">{{ $cell->getDescription() }}</span>
    @endunless

    {{ $slot }}
</{{ $interactive ? 'button' : 'div' }}>
