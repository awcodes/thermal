<?php

declare(strict_types=1);

use Awcodes\Thermal\Support\HeatmapScale;

/**
 * @param  array<int | float | null>  $values
 * @return array<int>
 */
function levelsFor(HeatmapScale $scale, array $values): array
{
    return array_map($scale->getLevel(...), $values);
}

it('puts everything at the baseline when there is no data', function () {
    $scale = HeatmapScale::fromValues([]);

    expect($scale->getThresholds())->toBe([])
        ->and($scale->getLevel(10))->toBe(0);
});

it('keeps zero, negative and missing values at the baseline', function () {
    $scale = HeatmapScale::fromValues([0, 5, 10, 20, 40, null]);

    expect(levelsFor($scale, [0, -3, null]))->toBe([0, 0, 0]);
});

it('spreads distinct values across the four populated levels', function () {
    $scale = HeatmapScale::fromValues([1, 2, 3, 4, 5, 6, 7, 8]);

    expect(levelsFor($scale, [1, 2, 3, 4, 5, 6, 7, 8]))->toBe([1, 1, 2, 2, 3, 3, 4, 4]);
});

it('keeps an outlier from flattening the rest of the data', function () {
    $values = [3, 5, 8, 12, 15, 20, 24, 10_000];
    $scale = HeatmapScale::fromValues($values);

    expect(levelsFor($scale, $values))->toBe([1, 1, 2, 2, 3, 3, 4, 4])
        ->and($scale->getLevel(10_000))->toBe(4);

    // A linear quarter-of-max scale would put every value except the outlier at level 1.
    $linear = HeatmapScale::fromThresholds([1, 2500, 5000, 7500]);
    expect(levelsFor($linear, [3, 5, 8, 12, 15, 20, 24]))->each->toBe(1);
});

it('is not skewed by heavily repeated values', function () {
    $values = [...array_fill(0, 50, 1), 2, 3, 4];
    $scale = HeatmapScale::fromValues($values);

    expect(levelsFor($scale, [1, 2, 3, 4]))->toBe([1, 2, 3, 4]);
});

it('always gives the largest value the top level, even with sparse data', function (array $values, array $expected) {
    expect(levelsFor(HeatmapScale::fromValues($values), $values))->toBe($expected);
})->with([
    'one value' => [[7], [4]],
    'two values' => [[3, 50], [2, 4]],
    'three values' => [[1, 2, 3], [2, 3, 4]],
    'identical values' => [[9, 9, 9], [4, 4, 4]],
]);

it('treats custom thresholds as the minimum value of each level', function () {
    $scale = HeatmapScale::fromThresholds([100, 250, 500, 1000]);

    expect(levelsFor($scale, [0, 99, 100, 249, 250, 500, 999, 1000, 50_000]))
        ->toBe([0, 0, 1, 1, 2, 3, 3, 4, 4]);
});

it('supports fewer than four custom thresholds', function () {
    $scale = HeatmapScale::fromThresholds([10, 20]);

    expect(levelsFor($scale, [5, 10, 25]))->toBe([0, 1, 2]);
});

it('rejects invalid custom thresholds', function (array $thresholds) {
    HeatmapScale::fromThresholds($thresholds);
})->with([
    'empty' => [[]],
    'too many' => [[1, 2, 3, 4, 5]],
    'descending' => [[10, 5]],
    'duplicate' => [[5, 5]],
    'zero' => [[0, 5]],
    'non-numeric' => [['10']],
])->throws(InvalidArgumentException::class);
