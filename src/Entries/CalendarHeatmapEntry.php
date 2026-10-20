<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Entries;

use Awcodes\Thermal\Calendar\CalendarHeatmap;
use Carbon\CarbonImmutable;
use Closure;

class CalendarHeatmapEntry extends HeatmapEntry
{
    protected string $view = 'thermal::entries.calendar-heatmap-entry';

    protected int | Closure $weeks = 52;

    protected int | Closure $weekStartsOn = 0;

    protected CarbonImmutable | Closure | null $endDate = null;

    public function weeks(int | Closure $weeks): static
    {
        $this->weeks = $weeks;

        return $this;
    }

    /**
     * 0 for Sunday through 6 for Saturday.
     */
    public function weekStartsOn(int | Closure $day): static
    {
        $this->weekStartsOn = $day;

        return $this;
    }

    /**
     * The last day shown. Defaults to today.
     */
    public function endDate(CarbonImmutable | Closure | null $date): static
    {
        $this->endDate = $date;

        return $this;
    }

    public function getWeeks(): int
    {
        return $this->evaluate($this->weeks);
    }

    public function getWeekStartsOn(): int
    {
        return $this->evaluate($this->weekStartsOn);
    }

    public function getEndDate(): ?CarbonImmutable
    {
        return $this->evaluate($this->endDate);
    }

    public function getHeatmap(): CalendarHeatmap
    {
        return new CalendarHeatmap(
            data: $this->getData(),
            weeks: $this->getWeeks(),
            weekStartsOn: $this->getWeekStartsOn(),
            endDate: $this->getEndDate(),
            unit: $this->getUnit(),
            thresholds: $this->getThresholds(),
            tooltipUsing: $this->getCellTooltipCallback(),
            color: $this->getColor(),
            hasLegend: $this->hasLegend(),
            legendLessLabel: $this->getLegendLessLabel(),
            legendMoreLabel: $this->getLegendMoreLabel(),
            fillMissing: $this->getFillMissing(),
        );
    }
}
