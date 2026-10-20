<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Support;

use InvalidArgumentException;

/**
 * Maps values onto intensity levels: 0 is the baseline for zero, negative and
 * missing values, and 1 to 4 are the populated levels.
 */
final readonly class HeatmapScale
{
    public const MAX_LEVEL = 4;

    /**
     * @param  list<int | float>  $thresholds  The minimum value of each populated level, ascending.
     */
    private function __construct(private array $thresholds) {}

    /**
     * Thresholds are the minimum value of each populated level, so `[100, 250, 500, 1000]`
     * puts 250 to 499 at level 2. Positive values below the first threshold sit at the baseline.
     *
     * @param  array<mixed>  $thresholds
     */
    public static function fromThresholds(array $thresholds): self
    {
        $thresholds = array_values($thresholds);

        if ($thresholds === [] || count($thresholds) > self::MAX_LEVEL) {
            throw new InvalidArgumentException('Heatmap thresholds must contain between 1 and ' . self::MAX_LEVEL . ' values.');
        }

        foreach ($thresholds as $index => $threshold) {
            if (! is_int($threshold) && ! is_float($threshold)) {
                throw new InvalidArgumentException('Heatmap thresholds must be numeric.');
            }

            if ($threshold <= 0) {
                throw new InvalidArgumentException('Heatmap thresholds must be greater than zero.');
            }

            if ($index > 0 && $threshold <= $thresholds[$index - 1]) {
                throw new InvalidArgumentException('Heatmap thresholds must be in strictly ascending order.');
            }
        }

        return new self($thresholds);
    }

    /**
     * Ranks the distinct positive values and splits them into quartiles. Ranking distinct
     * values keeps a single outlier from flattening everything else into the lowest level,
     * and keeps heavily repeated values from collapsing every threshold onto one number.
     * The largest value always reaches the top level.
     *
     * @param  iterable<int | float | null>  $values
     */
    public static function fromValues(iterable $values): self
    {
        $distinct = [];

        foreach ($values as $value) {
            if ($value !== null && $value > 0) {
                $distinct[(string) $value] = $value;
            }
        }

        $distinct = array_values($distinct);
        sort($distinct);

        if ($distinct === []) {
            return new self([]);
        }

        $count = count($distinct);
        $thresholds = [];

        for ($level = 0; $level < self::MAX_LEVEL; $level++) {
            $thresholds[] = $distinct[intdiv($level * $count, self::MAX_LEVEL)];
        }

        return new self($thresholds);
    }

    public function getLevel(int | float | null $value): int
    {
        if ($value === null || $value <= 0) {
            return 0;
        }

        $level = 0;

        foreach ($this->thresholds as $threshold) {
            if ($value >= $threshold) {
                $level++;
            }
        }

        return $level;
    }

    /**
     * @return list<int | float>
     */
    public function getThresholds(): array
    {
        return $this->thresholds;
    }
}
