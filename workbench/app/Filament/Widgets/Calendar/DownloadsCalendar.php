<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\Calendar;

use Awcodes\Thermal\Widgets\CalendarHeatmapWidget;
use Carbon\CarbonImmutable;
use Workbench\App\Support\SampleData;

class DownloadsCalendar extends CalendarHeatmapWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Downloads';

    protected ?string $description = 'One 5,000-download launch day. Automatic scaling keeps the rest of the year readable.';

    protected string $color = 'danger';

    protected ?string $unit = 'download';

    protected function getData(): array
    {
        return SampleData::outlier();
    }

    protected function getEndDate(): CarbonImmutable
    {
        return SampleData::today();
    }
}
