<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;
use Workbench\App\Filament\Widgets\Calendar;

class CalendarDashboard extends Dashboard
{
    protected static string $routePath = 'calendar';

    protected static ?string $title = 'Calendar widgets';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 1;

    public function getWidgets(): array
    {
        return [
            Calendar\OrdersCalendar::class,
            Calendar\SignupsCalendar::class,
            Calendar\DeploymentsCalendar::class,
            Calendar\DownloadsCalendar::class,
            Calendar\TicketsCalendar::class,
            Calendar\CommitsCalendar::class,
        ];
    }

    public function getColumns(): int
    {
        return 1;
    }
}
