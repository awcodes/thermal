<?php

declare(strict_types=1);

use Awcodes\Thermal\Support\HeatmapCell;
use Illuminate\Support\Facades\Blade;

function renderCell(HeatmapCell $cell, bool $interactive = false): string
{
    return Blade::render(
        '<x-thermal::cell :cell="$cell" :interactive="$interactive" />',
        ['cell' => $cell, 'interactive' => $interactive],
    );
}

it('renders a non-interactive cell without button semantics', function () {
    $html = renderCell(HeatmapCell::make('CA')->value(10)->level(2)->label('California'));

    expect($html)
        ->toContain('<div')
        ->toContain('California, 10, moderate activity')
        ->toContain('data-level="2"')
        ->not->toContain('<button')
        ->not->toContain('cursor-pointer');
});

it('renders an interactive cell as a real button with its selected state', function () {
    $html = renderCell(HeatmapCell::make('CA')->value(10)->level(2)->selected(), interactive: true);

    expect($html)
        ->toContain('<button')
        ->toContain('type="button"')
        ->toContain('aria-pressed="true"');
});

it('renders zero and missing cells differently', function () {
    $zero = renderCell(HeatmapCell::make('a')->value(0));
    $missing = renderCell(HeatmapCell::make('b'));

    expect($zero)->toContain('bg-gray-100')->not->toContain('data-missing')
        ->and($missing)->toContain('data-missing="true"')->not->toContain('bg-gray-100');
});

it('renders a legend with every level and accessible text', function () {
    $html = Blade::render('<x-thermal::legend />');

    expect(substr_count($html, 'fi-thermal-cell'))->toBe(5)
        ->and($html)->toContain('Color scale from Less to More')
        ->not->toContain('data-key');
});

it('renders a legend with custom labels', function () {
    expect(Blade::render('<x-thermal::legend less="Quiet" more="Busy" />'))
        ->toContain('Quiet')
        ->toContain('Busy');
});
