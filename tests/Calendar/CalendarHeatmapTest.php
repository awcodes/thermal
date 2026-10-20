<?php

declare(strict_types=1);

use Awcodes\Thermal\Calendar\CalendarHeatmap;
use Awcodes\Thermal\Support\HeatmapCell;
use Carbon\CarbonImmutable;

// Tuesday, October 20, 2026.
const CALENDAR_END = '2026-10-20';

/**
 * @param  array<string, mixed>  $data
 */
function calendar(array $data = [], mixed ...$options): CalendarHeatmap
{
    return new CalendarHeatmap($data, ...['endDate' => CarbonImmutable::parse(CALENDAR_END), ...$options]);
}

it('shows the requested number of week columns ending on the end date', function () {
    $heatmap = calendar(weeks: 4);

    expect($heatmap->getColumns())->toHaveCount(4)
        ->each->toHaveCount(7)
        ->and($heatmap->getStartDate()->toDateString())->toBe('2026-09-27')
        ->and($heatmap->getEndDate()->toDateString())->toBe(CALENDAR_END);
});

it('aligns columns to the configured week start', function (int $weekStartsOn, string $start, string $firstWeekday) {
    $heatmap = calendar(weeks: 2, weekStartsOn: $weekStartsOn);

    expect($heatmap->getStartDate()->toDateString())->toBe($start)
        ->and($heatmap->getColumns()[0][0]?->getKey())->toBe($start)
        ->and($heatmap->getStartDate()->format('D'))->toBe($firstWeekday);
})->with([
    'Sunday' => [0, '2026-10-11', 'Sun'],
    'Monday' => [1, '2026-10-12', 'Mon'],
    'Saturday' => [6, '2026-10-10', 'Sat'],
]);

it('leaves days after the end date empty', function () {
    $lastWeek = calendar(weeks: 1)->getColumns()[0];

    // Sunday through Tuesday exist; Wednesday through Saturday are in the future.
    expect(array_map(fn (?HeatmapCell $cell) => $cell?->getKey(), $lastWeek))
        ->toBe(['2026-10-18', '2026-10-19', '2026-10-20', null, null, null, null]);
});

it('resolves sparse dates and keeps zero distinct from missing', function () {
    $cells = calendar(['2026-10-19' => 0, '2026-10-20' => 12], weeks: 1)->getCells();

    expect($cells['2026-10-18']->hasValue())->toBeFalse()
        ->and($cells['2026-10-19']->getValue())->toBe(0)
        ->and($cells['2026-10-19']->getLevel())->toBe(0)
        ->and($cells['2026-10-20']->getValue())->toBe(12)
        ->and($cells['2026-10-20']->getLevel())->toBe(4);
});

it('can treat days without data as a given value', function () {
    $cells = calendar(['2026-10-19' => 4, '2026-10-20' => null], weeks: 1, fillMissing: 0)->getCells();

    expect($cells['2026-10-18']->hasValue())->toBeTrue()
        ->and($cells['2026-10-18']->getValue())->toBe(0)
        ->and($cells['2026-10-20']->getValue())->toBe(0)
        ->and($cells['2026-10-19']->getValue())->toBe(4)
        ->and($cells['2026-10-19']->getLevel())->toBe(4);
});

it('ignores data outside the visible range when scaling', function () {
    $heatmap = calendar(['2020-01-01' => 1_000_000, '2026-10-19' => 3, '2026-10-20' => 6], weeks: 1);

    expect($heatmap->getScale()->getThresholds())->toBe([3, 3, 6, 6])
        ->and($heatmap->hasCell('2020-01-01'))->toBeFalse();
});

it('normalizes date keys and numeric strings from the database', function () {
    $cells = calendar(['2026-10-20 00:00:00' => '4', '2026-10-20 13:30:00' => '1.5'], weeks: 1)->getCells();

    expect($cells['2026-10-20']->getValue())->toBe(5.5);
});

it('rejects keys that are not dates and values that are not numbers', function (array $data) {
    calendar($data);
})->with([
    'bad key' => [['not a date' => 1]],
    'bad value' => [['2026-10-20' => 'lots']],
])->throws(InvalidArgumentException::class);

it('applies custom thresholds', function () {
    $cells = calendar(['2026-10-19' => 5, '2026-10-20' => 50], weeks: 1, thresholds: [10, 20, 30, 40])->getCells();

    expect($cells['2026-10-19']->getLevel())->toBe(0)
        ->and($cells['2026-10-20']->getLevel())->toBe(4);
});

it('labels and describes days', function () {
    $cell = calendar(['2026-10-06' => 42], weeks: 3, unit: 'order')->getCells()['2026-10-06'];

    expect($cell->getLabel())->toBe('October 6, 2026')
        ->and($cell->getTooltip())->toBe('42 orders on Oct 6, 2026')
        ->and($cell->getDescription())->toBe('October 6, 2026, 42 orders, very high activity')
        ->and(calendar(weeks: 1)->getCells()['2026-10-20']->getTooltip())->toBe('No data on Oct 20, 2026');
});

it('lets the consumer replace tooltips', function () {
    $cell = calendar(
        ['2026-10-20' => 3],
        weeks: 1,
        tooltipUsing: fn (HeatmapCell $cell): string => "Custom {$cell->getValue()}",
    )->getCells()['2026-10-20'];

    expect($cell->getTooltip())->toBe('Custom 3');
});

it('labels Monday, Wednesday and Friday in week order', function (int $weekStartsOn, array $labels) {
    expect(calendar(weekStartsOn: $weekStartsOn)->getWeekdayLabels())->toBe($labels);
})->with([
    'Sunday start' => [0, ['', 'Mon', '', 'Wed', '', 'Fri', '']],
    'Monday start' => [1, ['Mon', '', 'Wed', '', 'Fri', '', '']],
]);

it('labels each month at the column where it begins', function () {
    $labels = calendar(weeks: 12)->getMonthLabels();

    // The range starts Sunday, August 2, 2026.
    expect($labels)->toBe([0 => 'Aug', 5 => 'Sep', 9 => 'Oct']);
});

it('drops the first month label when the next one would overlap it', function () {
    // The range starts Sunday, September 27, so October's label sits in the second column.
    expect(calendar(weeks: 4)->getMonthLabels())->toBe([1 => 'Oct']);
});

it('marks the selected day', function () {
    $cells = calendar(weeks: 1, selectedKey: '2026-10-19')->getCells();

    expect($cells['2026-10-19']->isSelected())->toBeTrue()
        ->and($cells['2026-10-20']->isSelected())->toBeFalse();
});

it('rejects invalid ranges', function (array $options) {
    calendar(...$options);
})->with([
    'no weeks' => [['weeks' => 0]],
    'bad week start' => [['weekStartsOn' => 7]],
])->throws(InvalidArgumentException::class);
