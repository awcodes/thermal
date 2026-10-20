<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\Tiles;

use Awcodes\Thermal\Layouts\TileLayout;
use Awcodes\Thermal\Widgets\TileHeatmapWidget;
use Workbench\App\Layouts\ServerRoomLayout;
use Workbench\App\Support\SampleData;

class RackRequests extends TileHeatmapWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Requests per rack';

    protected ?string $description = 'A non-geographic layout with a normal distribution. The empty aisle row and column are part of the geometry.';

    protected string $color = 'success';

    protected ?string $unit = 'request';

    protected function getLayout(): TileLayout
    {
        return new ServerRoomLayout;
    }

    protected function getData(): array
    {
        return SampleData::perKey(array_keys($this->getLayout()->getTiles()));
    }
}
