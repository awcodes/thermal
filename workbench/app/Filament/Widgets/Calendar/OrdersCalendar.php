<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\Calendar;

use Awcodes\Thermal\Widgets\CalendarHeatmapWidget;
use Carbon\CarbonImmutable;
use Workbench\App\Support\SampleData;

class OrdersCalendar extends CalendarHeatmapWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Orders';

    protected ?string $description = 'A normal distribution, weeks starting on Sunday.';

    protected string $color = 'success';

    protected ?string $unit = 'order';

    protected function getData(): array
    {
        return SampleData::daily();
    }

    protected function getEndDate(): CarbonImmutable
    {
        return SampleData::today();
    }
}
