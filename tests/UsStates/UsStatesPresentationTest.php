<?php

declare(strict_types=1);

use Awcodes\Thermal\Entries\UsStatesHeatmapEntry;
use Awcodes\Thermal\Layouts\UsStatesLayout;
use Awcodes\Thermal\Tests\Fixtures\CampaignInfolist;
use Awcodes\Thermal\Tests\Fixtures\LeadsStatesWidget;
use Awcodes\Thermal\Tiles\TileHeatmap;
use Livewire\Livewire;

use function Pest\Livewire\livewire;

afterEach(function () {
    LeadsStatesWidget::$interactive = false;
});

it('resolves sample values onto states', function () {
    $cells = (new TileHeatmap(
        ['CA' => 1284, 'tx' => '894', 'Georgia' => 542, 'georgia' => 8, 'WY' => 0, 'PR' => 99],
        layout: new UsStatesLayout,
        unit: 'lead',
    ))->getCells();

    expect($cells)->toHaveCount(51)
        ->and($cells['CA']->getDescription())->toBe('California, 1,284 leads, very high activity')
        ->and($cells['CA']->getTooltip())->toBe('California — 1,284 leads')
        ->and($cells['TX']->getValue())->toBe(894)
        ->and($cells['GA']->getValue())->toBe(550)
        ->and($cells['WY']->hasValue())->toBeTrue()
        ->and($cells['WY']->getLevel())->toBe(0)
        ->and($cells['NV']->hasValue())->toBeFalse();
});

it('renders the U.S. states widget', function () {
    livewire(LeadsStatesWidget::class)
        ->assertOk()
        ->assertSee('Lead activity across the U.S.')
        ->assertSeeHtml('data-key="DC"')
        ->assertSee('California, 1,284 leads, very high activity')
        ->assertSee('Nevada, no data')
        ->assertSeeHtml('grid-template-columns: repeat(11, var(--thermal-tile-size))')
        ->assertDontSeeHtml('<button');
});

it('renders the U.S. states entry inside a schema', function () {
    Livewire::test(CampaignInfolist::class)
        ->assertOk()
        ->assertSee('Lead distribution')
        ->assertSeeHtml('data-key="GA"')
        ->assertSee('Georgia, 542 leads');
});

it('resolves the same cells for the widget and the entry', function () {
    $entry = UsStatesHeatmapEntry::make('lead_distribution')
        ->data(['CA' => 1284, 'tx' => 894, 'Georgia' => 542, 'WY' => 0])
        ->unit('lead');

    expect(array_map(fn ($cell) => $cell->getDescription(), $entry->getHeatmap()->getCells()))
        ->toBe(array_map(fn ($cell) => $cell->getDescription(), (new LeadsStatesWidget)->getHeatmap()->getCells()));
});

it('selects states by their canonical key when interactive', function () {
    LeadsStatesWidget::$interactive = true;

    livewire(LeadsStatesWidget::class)
        ->call('selectCell', 'CA')
        ->assertSet('selectedCell', 'CA')
        ->assertSeeHtml('aria-pressed="true"')
        ->call('selectCell', 'ca')
        ->call('selectCell', 'PR')
        ->assertSet('selectedCell', 'CA')
        ->assertSet('events', ['selected:CA']);
});
