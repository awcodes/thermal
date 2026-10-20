<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;
use Workbench\App\Filament\Widgets\Tiles;

class TileDashboard extends Dashboard
{
    protected static string $routePath = 'tiles';

    protected static ?string $title = 'Tile widgets';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?int $navigationSort = 3;

    public function getWidgets(): array
    {
        return [
            Tiles\RackRequests::class,
            Tiles\RackIncidents::class,
            Tiles\RackTraffic::class,
            Tiles\RackPower::class,
        ];
    }

    public function getColumns(): int
    {
        return 3;
    }
}
