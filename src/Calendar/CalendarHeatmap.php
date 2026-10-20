<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Calendar;

use Awcodes\Thermal\Support\HeatmapCell;
use Awcodes\Thermal\Support\HeatmapScale;
use Carbon\CarbonImmutable;
use Closure;
use InvalidArgumentException;
use Throwable;

/**
 * Resolves daily values into a GitHub-style grid of week columns and weekday rows.
 */
final class CalendarHeatmap
{
    private readonly CarbonImmutable $start;

    private readonly CarbonImmutable $end;

    private readonly HeatmapScale $scale;

    /** @var array<string, int | float | null> */
    private readonly array $values;

    /** @var list<list<HeatmapCell | null>>|null */
    private ?array $columns = null;

    /**
     * @param  array<string, mixed>  $data  Values keyed by date, such as `['2026-10-06' => 42]`.
     * @param  int  $weekStartsOn  0 for Sunday through 6 for Saturday.
     * @param  array<mixed>|null  $thresholds  Custom level thresholds, or null to scale automatically.
     * @param  (Closure(HeatmapCell): ?string)|null  $tooltipUsing
     * @param  int|float|null  $fillMissing  A value for days without data, or null to keep them missing.
     */
    public function __construct(
        array $data,
        private readonly int $weeks = 52,
        int $weekStartsOn = 0,
        ?CarbonImmutable $endDate = null,
        private readonly ?string $unit = null,
        ?array $thresholds = null,
        private readonly ?string $selectedKey = null,
        private readonly ?Closure $tooltipUsing = null,
        private readonly string $color = 'primary',
        private readonly bool $hasLegend = true,
        private readonly ?string $legendLessLabel = null,
        private readonly ?string $legendMoreLabel = null,
        private readonly int | float | null $fillMissing = null,
    ) {
        if ($weeks < 1) {
            throw new InvalidArgumentException('A calendar heatmap must show at least one week.');
        }

        if ($weekStartsOn < 0 || $weekStartsOn > 6) {
            throw new InvalidArgumentException('The week must start on a day from 0 (Sunday) to 6 (Saturday).');
        }

        $this->end = ($endDate ?? CarbonImmutable::today())->startOfDay();
        $this->start = $this->end
            ->subDays(($this->end->dayOfWeek - $weekStartsOn + 7) % 7)
            ->subWeeks($weeks - 1);

        $this->values = $this->normalize($data);

        $this->scale = $thresholds === null
            ? HeatmapScale::fromValues($this->values)
            : HeatmapScale::fromThresholds($thresholds);
    }

    /**
     * One entry per week, each holding seven days in week order. Days after the end date are null.
     *
     * @return list<list<HeatmapCell | null>>
     */
    public function getColumns(): array
    {
        if ($this->columns !== null) {
            return $this->columns;
        }

        $columns = [];

        for ($week = 0; $week < $this->weeks; $week++) {
            $days = [];

            for ($day = 0; $day < 7; $day++) {
                $date = $this->start->addDays(($week * 7) + $day);
                $days[] = $date->greaterThan($this->end) ? null : $this->makeCell($date);
            }

            $columns[] = $days;
        }

        return $this->columns = $columns;
    }

    /**
     * Short month names keyed by the week column where each month begins.
     *
     * @return array<int, string>
     */
    public function getMonthLabels(): array
    {
        $labels = [];

        for ($week = 0; $week < $this->weeks; $week++) {
            $weekStart = $this->start->addWeeks($week);

            if ($week === 0 || $weekStart->day <= 7) {
                $labels[$week] = $weekStart->translatedFormat('M');
            }
        }

        // The first column usually starts partway through a month, so drop its label
        // when the next month's label would sit close enough to overlap it.
        $next = array_keys($labels)[1] ?? null;

        if ($next !== null && $next < 3) {
            unset($labels[0]);
        }

        return $labels;
    }

    /**
     * Seven labels in week order. Only Monday, Wednesday and Friday are named, to keep the axis readable.
     *
     * @return list<string>
     */
    public function getWeekdayLabels(): array
    {
        $labels = [];

        for ($day = 0; $day < 7; $day++) {
            $date = $this->start->addDays($day);

            $labels[] = in_array($date->dayOfWeek, [1, 3, 5], true)
                ? $date->translatedFormat('D')
                : '';
        }

        return $labels;
    }

    /**
     * Every resolved cell keyed by date, which is what interactive selection is checked against.
     *
     * @return array<string, HeatmapCell>
     */
    public function getCells(): array
    {
        $cells = [];

        foreach ($this->getColumns() as $days) {
            foreach ($days as $cell) {
                if ($cell !== null) {
                    $cells[$cell->getKey()] = $cell;
                }
            }
        }

        return $cells;
    }

    public function hasCell(string $key): bool
    {
        return array_key_exists($key, $this->getCells());
    }

    public function getStartDate(): CarbonImmutable
    {
        return $this->start;
    }

    public function getEndDate(): CarbonImmutable
    {
        return $this->end;
    }

    public function getWeeks(): int
    {
        return $this->weeks;
    }

    public function getScale(): HeatmapScale
    {
        return $this->scale;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function hasLegend(): bool
    {
        return $this->hasLegend;
    }

    public function getLegendLessLabel(): ?string
    {
        return $this->legendLessLabel;
    }

    public function getLegendMoreLabel(): ?string
    {
        return $this->legendMoreLabel;
    }

    private function makeCell(CarbonImmutable $date): HeatmapCell
    {
        $key = $date->toDateString();
        $value = $this->values[$key] ?? $this->fillMissing;

        $cell = HeatmapCell::make($key)
            ->value($value)
            ->level($this->scale->getLevel($value))
            ->label($date->translatedFormat('F j, Y'))
            ->unit($this->unit)
            ->selected($key === $this->selectedKey);

        $shortDate = $date->translatedFormat('M j, Y');

        $cell->tooltip($cell->hasValue()
            ? __('thermal::heatmap.calendar.tooltip', ['value' => $cell->getFormattedValue(), 'date' => $shortDate])
            : __('thermal::heatmap.calendar.tooltip_missing', ['date' => $shortDate]));

        if ($this->tooltipUsing instanceof Closure && filled($tooltip = ($this->tooltipUsing)($cell))) {
            $cell->tooltip($tooltip);
        }

        return $cell;
    }

    /**
     * Keeps the values inside the visible range, keyed by `Y-m-d`. Keys that are not dates
     * are rejected, numeric strings from database drivers are cast, and values for the same
     * day are added together.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, int | float | null>
     */
    private function normalize(array $data): array
    {
        $values = [];

        foreach ($data as $key => $value) {
            try {
                $date = CarbonImmutable::parse((string) $key)->startOfDay();
            } catch (Throwable $exception) {
                throw new InvalidArgumentException("Calendar heatmap data must be keyed by date, [{$key}] given.", $exception->getCode(), previous: $exception);
            }

            if ($date->lessThan($this->start) || $date->greaterThan($this->end)) {
                continue;
            }

            if (is_string($value) && is_numeric($value)) {
                $value += 0;
            }

            if ($value !== null && ! is_int($value) && ! is_float($value)) {
                throw new InvalidArgumentException("Calendar heatmap values must be numeric or null, [{$key}] is not.");
            }

            $dateKey = $date->toDateString();

            $values[$dateKey] = $value === null
                ? ($values[$dateKey] ?? null)
                : ($values[$dateKey] ?? 0) + $value;
        }

        return $values;
    }
}
