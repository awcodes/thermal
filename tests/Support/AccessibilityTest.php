<?php

declare(strict_types=1);

use Awcodes\Thermal\Tests\Fixtures\CustomerInfolist;
use Awcodes\Thermal\Tests\Fixtures\OrdersCalendarWidget;
use Awcodes\Thermal\Tests\Fixtures\RackHealthWidget;
use Awcodes\Thermal\Tests\Fixtures\RackInfolist;
use Livewire\Livewire;

use function Pest\Livewire\livewire;

afterEach(function () {
    OrdersCalendarWidget::$interactive = false;
    RackHealthWidget::$interactive = false;
});

it('labels the visualization with the widget heading or the entry label', function () {
    livewire(RackHealthWidget::class)->assertSeeHtml('role="group" aria-label="Rack health"');
    livewire(OrdersCalendarWidget::class)->assertSeeHtml('role="group" aria-label="Orders"');
    Livewire::test(RackInfolist::class)->assertSeeHtml('aria-label="Racks"');
    Livewire::test(CustomerInfolist::class)->assertSeeHtml('aria-label="Activity"');
});

it('leaves non-interactive grids out of the tab order', function () {
    livewire(RackHealthWidget::class)
        ->assertDontSeeHtml('tabindex=')
        ->assertDontSeeHtml('data-row=')
        ->assertDontSeeHtml('x-on:keydown');
});

it('makes an interactive grid a single tab stop with arrow key navigation', function () {
    RackHealthWidget::$interactive = true;

    $html = livewire(RackHealthWidget::class)->html();

    expect(substr_count($html, 'tabindex="0"'))->toBe(1)
        ->and(substr_count($html, 'tabindex="-1"'))->toBe(3)
        ->and($html)->toContain('x-on:keydown="move($event)"')
        ->toContain('data-row="2"')
        ->toContain('data-column="3"');
});

it('puts the tab stop on the first cell, then on the selected cell', function () {
    RackHealthWidget::$interactive = true;

    $component = livewire(RackHealthWidget::class);

    expect($component->html())->toMatch('/data-key="east-1"[^>]*tabindex="0"|tabindex="0"[^>]*data-key="east-1"/s');

    $component->call('selectCell', 'west-2');

    expect($component->html())->toMatch('/tabindex="0"\s+data-row="2"\s+data-column="3"/');
});

it('positions calendar cells by weekday row and week column', function () {
    OrdersCalendarWidget::$interactive = true;

    // October 6, 2026 is the Tuesday of the second of four weeks starting on Sunday.
    expect(livewire(OrdersCalendarWidget::class)->html())
        ->toMatch('/data-row="3"\s+data-column="2"\s+data-key="2026-10-06"/');
});

it('puts the calendar tab stop on the most recent day', function () {
    OrdersCalendarWidget::$interactive = true;

    expect(livewire(OrdersCalendarWidget::class)->html())
        ->toMatch('/tabindex="0"\s+data-row="3"\s+data-column="4"\s+data-key="2026-10-20"/');
});
