<?php

declare(strict_types=1);

namespace Workbench\App\Support;

use Carbon\CarbonImmutable;

/**
 * Deterministic fixture data, so the Workbench looks the same on every load.
 */
final class SampleData
{
    /**
     * A busy weekday, quieter weekend pattern with some noise.
     *
     * @return array<string, int>
     */
    public static function daily(int $days = 365, int $seed = 1): array
    {
        mt_srand($seed);

        $data = [];

        foreach (self::days($days) as $date) {
            $base = $date->isWeekend() ? 4 : 14;
            $data[$date->toDateString()] = max(0, $base + mt_rand(-6, 10));
        }

        return $data;
    }

    /**
     * Only a handful of days have activity. Every other day is missing.
     *
     * @return array<string, int>
     */
    public static function sparse(int $days = 365, int $seed = 2): array
    {
        mt_srand($seed);

        $data = [];

        foreach (self::days($days) as $date) {
            if (mt_rand(1, 12) === 1) {
                $data[$date->toDateString()] = mt_rand(1, 9);
            }
        }

        return $data;
    }

    /**
     * Weekdays report explicit zeros or values, weekends report nothing at all.
     *
     * @return array<string, int>
     */
    public static function zerosAndMissing(int $days = 365, int $seed = 3): array
    {
        mt_srand($seed);

        $data = [];

        foreach (self::days($days) as $date) {
            if (! $date->isWeekend()) {
                $data[$date->toDateString()] = mt_rand(0, 2) === 0 ? 0 : mt_rand(1, 20);
            }
        }

        return $data;
    }

    /**
     * Ordinary daily values plus one launch-day spike far above the rest.
     *
     * @return array<string, int>
     */
    public static function outlier(int $days = 365, int $seed = 4): array
    {
        $data = self::daily($days, $seed);
        $data[CarbonImmutable::today()->subDays(40)->toDateString()] = 5_000;

        return $data;
    }

    /**
     * One value per key, spread fairly evenly between the bounds.
     *
     * @param  iterable<string>  $keys
     * @return array<string, int>
     */
    public static function perKey(iterable $keys, int $min = 20, int $max = 400, int $seed = 1): array
    {
        mt_srand($seed);

        $data = [];

        foreach ($keys as $key) {
            $data[$key] = mt_rand($min, $max);
        }

        return $data;
    }

    /**
     * @return iterable<CarbonImmutable>
     */
    private static function days(int $days): iterable
    {
        $today = CarbonImmutable::today();

        for ($offset = $days - 1; $offset >= 0; $offset--) {
            yield $today->subDays($offset);
        }
    }
}
