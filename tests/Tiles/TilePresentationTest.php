<?php

declare(strict_types=1);

use Awcodes\Thermal\Entries\TileHeatmapEntry;
use Awcodes\Thermal\Tests\Fixtures\RackHealthWidget;
use Awcodes\Thermal\Tests\Fixtures\RackInfolist;
use Awcodes\Thermal\Tests\Fixtures\RackLayout;
use Awcodes\Thermal\Tiles\TileHeatmap;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;

use function Pest\Livewire\livewire;

afterEach(function () {
    RackHealthWidget::$interactive = false;
});

it('renders the tile widget', function () {
    livewire(RackHealthWidget::class)
        ->assertOk()
        ->assertSee('Rack health')
        ->assertSeeHtml('data-key="east-1"')
        ->assertSee('West 1, 1,284 requests, very high activity')
        ->assertSee('West 2, no data')
        ->assertSee('Color scale from Less to More')
        ->assertSeeHtml('--color-500:var(--success-500)')
        ->assertDontSeeHtml('<button')
        ->assertDontSeeHtml('wire:click');
});

it('places tiles at their layout coordinates and keeps empty columns', function () {
    $html = Blade::render('<x-thermal::tiles :heatmap="$heatmap" />', [
        'heatmap' => new TileHeatmap(['east-1' => 1], layout: new RackLayout),
    ]);

    expect($html)
        ->toContain('grid-template-columns: repeat(3, var(--thermal-tile-size))')
        ->toContain('grid-template-rows: repeat(2, var(--thermal-tile-size))')
        ->toContain('grid-row: 2; grid-column: 3;')
        ->toContain('>W2</span>')
        ->and(substr_count($html, 'data-key='))->toBe(4);
});

it('renders the tile entry inside a schema', function () {
    Livewire::test(RackInfolist::class)
        ->assertOk()
        ->assertSee('Racks')
        ->assertSeeHtml('data-key="west-1"')
        ->assertSee('West 1, 1,284 requests, very high activity')
        ->assertSeeHtml('--color-500:var(--success-500)');
});

it('resolves the same cells for the widget and the entry', function () {
    $widgetCells = (new RackHealthWidget)->getHeatmap()->getCells();

    $entry = TileHeatmapEntry::make('racks')
        ->data(['east-1' => 83, 'east-2' => 0, 'west-1' => 1284])
        ->layout(fn () => new RackLayout)
        ->unit('request');

    expect(array_map(fn ($cell) => $cell->getDescription(), $entry->getHeatmap()->getCells()))
        ->toBe(array_map(fn ($cell) => $cell->getDescription(), $widgetCells));
});

it('requires an entry layout', function () {
    TileHeatmapEntry::make('racks')->data([])->getHeatmap();
})->throws(LogicException::class, 'needs a layout');

it('ignores selection when the widget is not interactive', function () {
    livewire(RackHealthWidget::class)
        ->call('selectCell', 'east-1')
        ->assertSet('selectedCell', null)
        ->assertSet('events', []);
});

it('selects and deselects tiles by key when interactive', function () {
    RackHealthWidget::$interactive = true;

    livewire(RackHealthWidget::class)
        ->assertSeeHtml('<button')
        ->assertSeeHtml('wire:click="selectCell(')
        ->call('selectCell', 'west-2')
        ->assertSet('selectedCell', 'west-2')
        ->assertSeeHtml('aria-pressed="true"')
        ->call('selectCell', 'east-1')
        ->assertSet('selectedCell', 'east-1')
        ->call('selectCell', 'east-1')
        ->assertSet('selectedCell', null)
        ->assertSet('events', ['selected:west-2', 'selected:east-1', 'deselected:east-1']);
});

it('ignores keys outside the layout', function () {
    RackHealthWidget::$interactive = true;

    livewire(RackHealthWidget::class)
        ->call('selectCell', 'north-1')
        ->assertSet('selectedCell', null)
        ->assertSet('events', []);
});
