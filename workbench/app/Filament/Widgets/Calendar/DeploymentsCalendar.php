<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\Calendar;

use Awcodes\Thermal\Widgets\CalendarHeatmapWidget;
use Workbench\App\Support\SampleData;

class DeploymentsCalendar extends CalendarHeatmapWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Deployments';

    protected ?string $description = 'Explicit zeros on weekdays (solid gray) and missing weekends (outlined).';

    protected string $color = 'warning';

    protected int $weekStartsOn = 1;

    protected ?string $unit = 'deployment';

    protected function getData(): array
    {
        return SampleData::zerosAndMissing();
    }
}
