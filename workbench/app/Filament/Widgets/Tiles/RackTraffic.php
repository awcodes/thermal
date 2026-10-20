<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\Tiles;

use Awcodes\Thermal\Layouts\TileLayout;
use Awcodes\Thermal\Widgets\TileHeatmapWidget;
use Workbench\App\Layouts\ServerRoomLayout;
use Workbench\App\Support\SampleData;

class RackTraffic extends TileHeatmapWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Egress traffic';

    protected ?string $description = 'Narrow: tiles shrink, then scroll. One rack is a large outlier.';

    protected string $color = 'info';

    protected ?string $unit = 'gigabyte';

    protected int | string | array $columnSpan = 1;

    protected function getLayout(): TileLayout
    {
        return new ServerRoomLayout;
    }

    protected function getData(): array
    {
        return [
            ...SampleData::perKey(array_keys($this->getLayout()->getTiles()), 10, 90, seed: 3),
            'a-7' => 25_000,
        ];
    }
}
