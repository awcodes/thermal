<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\Calendar;

use Awcodes\Thermal\Widgets\CalendarHeatmapWidget;
use Workbench\App\Support\SampleData;

class SignupsCalendar extends CalendarHeatmapWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Sign-ups';

    protected ?string $description = 'Sparse data, weeks starting on Monday. Days without data are outlined.';

    protected string $color = 'info';

    protected int $weekStartsOn = 1;

    protected ?string $unit = 'sign-up';

    protected function getData(): array
    {
        return SampleData::sparse();
    }
}
