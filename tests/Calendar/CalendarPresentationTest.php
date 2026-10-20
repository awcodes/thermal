<?php

declare(strict_types=1);

use Awcodes\Thermal\Entries\CalendarHeatmapEntry;
use Awcodes\Thermal\Tests\Fixtures\CustomerInfolist;
use Awcodes\Thermal\Tests\Fixtures\OrdersCalendarWidget;
use Carbon\CarbonImmutable;
use Livewire\Livewire;

use function Pest\Livewire\livewire;

afterEach(function () {
    OrdersCalendarWidget::$interactive = false;
});

it('renders the calendar widget', function () {
    livewire(OrdersCalendarWidget::class)
        ->assertOk()
        ->assertSee('Orders')
        ->assertSeeHtml('data-key="2026-10-06"')
        ->assertSee('October 6, 2026, 42 orders, very high activity')
        ->assertSee('Color scale from Less to More')
        ->assertSeeHtml('--color-500:var(--success-500)')
        ->assertDontSeeHtml('<button')
        ->assertDontSeeHtml('wire:click');
});

it('renders the calendar entry inside a schema', function () {
    Livewire::test(CustomerInfolist::class)
        ->assertOk()
        ->assertSee('Activity')
        ->assertSeeHtml('data-key="2026-10-06"')
        ->assertSee('October 6, 2026, 42 orders, very high activity')
        ->assertSeeHtml('--color-500:var(--success-500)');
});

it('resolves the same cells for the widget and the entry', function () {
    $widgetCells = (new OrdersCalendarWidget)->getHeatmap()->getCells();

    $entry = CalendarHeatmapEntry::make('activity')
        ->data(['2026-10-06' => 42, '2026-10-19' => 0])
        ->weeks(4)
        ->endDate(CarbonImmutable::parse('2026-10-20'))
        ->unit('order');

    expect(array_map(fn ($cell) => $cell->getDescription(), $entry->getHeatmap()->getCells()))
        ->toBe(array_map(fn ($cell) => $cell->getDescription(), $widgetCells));
});

it('fills missing days the same way for the widget and the entry', function () {
    $widget = new class extends OrdersCalendarWidget
    {
        protected int | float | null $fillMissing = 0;
    };

    $entry = CalendarHeatmapEntry::make('activity')
        ->data(['2026-10-06' => 42, '2026-10-19' => 0])
        ->weeks(4)
        ->endDate(CarbonImmutable::parse('2026-10-20'))
        ->fillMissing(0);

    expect($widget->getHeatmap()->getCells()['2026-10-07']->getValue())->toBe(0)
        ->and($entry->getHeatmap()->getCells()['2026-10-07']->getValue())->toBe(0);
});

it('ignores selection when the widget is not interactive', function () {
    livewire(OrdersCalendarWidget::class)
        ->call('selectCell', '2026-10-06')
        ->assertSet('selectedCell', null)
        ->assertSet('events', []);
});

it('selects and deselects cells by key when interactive', function () {
    OrdersCalendarWidget::$interactive = true;

    livewire(OrdersCalendarWidget::class)
        ->assertSeeHtml('<button')
        ->assertSeeHtml('wire:click="selectCell(')
        ->call('selectCell', '2026-10-06')
        ->assertSet('selectedCell', '2026-10-06')
        ->assertSeeHtml('aria-pressed="true"')
        ->call('selectCell', '2026-10-06')
        ->assertSet('selectedCell', null)
        ->assertSet('events', ['selected:2026-10-06', 'deselected:2026-10-06']);
});

it('ignores keys outside the calendar', function (string $key) {
    OrdersCalendarWidget::$interactive = true;

    livewire(OrdersCalendarWidget::class)
        ->call('selectCell', $key)
        ->assertSet('selectedCell', null)
        ->assertSet('events', []);
})->with(['before the range' => '2020-01-01', 'in the future' => '2026-10-21', 'not a date' => 'nope']);
