<?php

declare(strict_types=1);

use Awcodes\Thermal\Layouts\Tile;
use Awcodes\Thermal\Layouts\TileLayout;
use Awcodes\Thermal\Support\HeatmapCell;
use Awcodes\Thermal\Tests\Fixtures\RackLayout;
use Awcodes\Thermal\Tiles\TileHeatmap;

/**
 * @param  array<array-key, mixed>  $data
 */
function tileHeatmap(array $data = [], mixed ...$options): TileHeatmap
{
    return new TileHeatmap($data, ...['layout' => new RackLayout, ...$options]);
}

it('resolves one cell per tile in layout order', function () {
    expect(array_keys(tileHeatmap()->getCells()))->toBe(['east-1', 'east-2', 'west-1', 'west-2']);
});

it('joins data to tiles by key and keeps zero distinct from missing', function () {
    $cells = tileHeatmap(['east-1' => 83, 'east-2' => 0, 'west-1' => null])->getCells();

    expect($cells['east-1']->getValue())->toBe(83)
        ->and($cells['east-1']->getLevel())->toBe(4)
        ->and($cells['east-2']->hasValue())->toBeTrue()
        ->and($cells['east-2']->getLevel())->toBe(0)
        ->and($cells['west-1']->hasValue())->toBeFalse()
        ->and($cells['west-2']->hasValue())->toBeFalse();
});

it('can treat tiles without data as a given value', function () {
    $cells = tileHeatmap(['east-1' => 5], fillMissing: 0)->getCells();

    expect($cells['west-2']->getValue())->toBe(0)
        ->and($cells['east-1']->getLevel())->toBe(4);
});

it('ignores keys outside the layout, including when scaling', function () {
    $heatmap = tileHeatmap(['east-1' => 3, 'west-1' => 6, 'north-1' => 1_000_000]);

    expect($heatmap->getScale()->getThresholds())->toBe([3, 3, 6, 6])
        ->and($heatmap->hasCell('north-1'))->toBeFalse()
        ->and($heatmap->getCell('north-1'))->toBeNull();
});

it('casts numeric strings and integer keys', function () {
    $layout = new TileLayout([Tile::make('7', 'Seven', 1, 1)]);
    $heatmap = new TileHeatmap([7 => '4.5'], layout: $layout);

    expect($heatmap->getCell('7')?->getValue())->toBe(4.5);
});

it('rejects values that are not numbers', function () {
    tileHeatmap(['east-1' => 'lots']);
})->throws(InvalidArgumentException::class);

it('applies custom thresholds', function () {
    $cells = tileHeatmap(['east-1' => 5, 'west-1' => 50], thresholds: [10, 20, 30, 40])->getCells();

    expect($cells['east-1']->getLevel())->toBe(0)
        ->and($cells['west-1']->getLevel())->toBe(4);
});

it('labels, describes and tooltips cells with the tile name', function () {
    $cells = tileHeatmap(['east-1' => 1284], unit: 'lead')->getCells();

    expect($cells['east-1']->getLabel())->toBe('East 1')
        ->and($cells['east-1']->getTooltip())->toBe('East 1 — 1,284 leads')
        ->and($cells['east-1']->getDescription())->toBe('East 1, 1,284 leads, very high activity')
        ->and($cells['west-2']->getTooltip())->toBe('West 2 — no data');
});

it('lets the consumer replace tooltips', function () {
    $cell = tileHeatmap(
        ['east-1' => 3],
        tooltipUsing: fn (HeatmapCell $cell): ?string => $cell->hasValue() ? "Custom {$cell->getValue()}" : null,
    )->getCells();

    expect($cell['east-1']->getTooltip())->toBe('Custom 3')
        ->and($cell['west-1']->getTooltip())->toBe('West 1 — no data');
});

it('marks the selected tile', function () {
    $cells = tileHeatmap(selectedKey: 'west-1')->getCells();

    expect($cells['west-1']->isSelected())->toBeTrue()
        ->and($cells['east-1']->isSelected())->toBeFalse();
});
