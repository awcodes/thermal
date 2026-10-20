<?php

declare(strict_types=1);

use Awcodes\Thermal\Support\HeatmapCell;

it('keeps an explicit zero distinct from missing data', function () {
    $zero = HeatmapCell::make('TX')->value(0)->label('Texas');
    $missing = HeatmapCell::make('GA')->label('Georgia');

    expect($zero->hasValue())->toBeTrue()
        ->and($zero->getFormattedValue())->toBe('0')
        ->and($zero->getDescription())->toBe('Texas, 0, no activity')
        ->and($missing->hasValue())->toBeFalse()
        ->and($missing->getFormattedValue())->toBeNull()
        ->and($missing->getDescription())->toBe('Georgia, no data');
});

it('describes a populated cell for assistive technology', function () {
    $cell = HeatmapCell::make('CA')->value(1284)->level(3)->label('California')->unit('lead');

    expect($cell->getDescription())->toBe('California, 1,284 leads, high activity')
        ->and($cell->getTooltip())->toBe('California — 1,284 leads');
});

it('pluralizes the unit to match the value', function () {
    expect(HeatmapCell::make('a')->value(1)->unit('order')->getFormattedValue())->toBe('1 order')
        ->and(HeatmapCell::make('b')->value(2)->unit('order')->getFormattedValue())->toBe('2 orders');
});

it('falls back to the key as its label', function () {
    expect(HeatmapCell::make('east-1')->getLabel())->toBe('east-1');
});

it('clamps the level to the scale and ignores it for missing data', function () {
    expect(HeatmapCell::make('a')->value(1)->level(9)->getLevel())->toBe(4)
        ->and(HeatmapCell::make('b')->value(1)->level(-1)->getLevel())->toBe(0)
        ->and(HeatmapCell::make('c')->level(3)->getLevel())->toBe(0);
});

it('uses a custom tooltip when given one', function () {
    expect(HeatmapCell::make('a')->value(1)->tooltip('Custom')->getTooltip())->toBe('Custom');
});
