<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\Calendar;

use Awcodes\Thermal\Widgets\CalendarHeatmapWidget;
use Carbon\CarbonImmutable;
use Workbench\App\Support\SampleData;

class TicketsCalendar extends CalendarHeatmapWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Support tickets';

    protected ?string $description = 'Custom thresholds of 5, 10, 15 and 20 tickets over the last 26 weeks, without a legend.';

    protected int $weeks = 26;

    protected ?string $unit = 'ticket';

    protected bool $hasLegend = false;

    protected function getData(): array
    {
        return SampleData::daily(seed: 5);
    }

    protected function getThresholds(): array
    {
        return [5, 10, 15, 20];
    }

    protected function getEndDate(): CarbonImmutable
    {
        return SampleData::today();
    }
}
