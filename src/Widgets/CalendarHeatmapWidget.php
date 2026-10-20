<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Widgets;

use Awcodes\Thermal\Calendar\CalendarHeatmap;
use Carbon\CarbonImmutable;

abstract class CalendarHeatmapWidget extends HeatmapWidget
{
    protected int $weeks = 52;

    /**
     * 0 for Sunday through 6 for Saturday.
     */
    protected int $weekStartsOn = 0;

    protected string $view = 'thermal::widgets.calendar-heatmap-widget';

    public function getHeatmap(): CalendarHeatmap
    {
        return new CalendarHeatmap(
            data: $this->getData(),
            weeks: $this->getWeeks(),
            weekStartsOn: $this->getWeekStartsOn(),
            endDate: $this->getEndDate(),
            unit: $this->getUnit(),
            thresholds: $this->getThresholds(),
            selectedKey: $this->selectedCell,
            tooltipUsing: $this->getCellTooltip(...),
            color: $this->getColor(),
            hasLegend: $this->hasLegend(),
            legendLessLabel: $this->getLegendLessLabel(),
            legendMoreLabel: $this->getLegendMoreLabel(),
            fillMissing: $this->getFillMissing(),
        );
    }

    protected function getWeeks(): int
    {
        return $this->weeks;
    }

    protected function getWeekStartsOn(): int
    {
        return $this->weekStartsOn;
    }

    /**
     * The last day shown, or null for today.
     */
    protected function getEndDate(): ?CarbonImmutable
    {
        return null;
    }

    protected function hasCell(string $key): bool
    {
        return $this->getHeatmap()->hasCell($key);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'heatmap' => $this->getHeatmap(),
            'interactive' => $this->isInteractive(),
        ];
    }
}
