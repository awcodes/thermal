<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\Tiles;

use Awcodes\Thermal\Layouts\TileLayout;
use Awcodes\Thermal\Support\HeatmapCell;
use Awcodes\Thermal\Widgets\TileHeatmapWidget;
use Workbench\App\Layouts\ServerRoomLayout;
use Workbench\App\Support\SampleData;

class RackPower extends TileHeatmapWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Power draw';

    protected ?string $description = 'Custom thresholds of 4, 6, 8 and 10 kW, custom legend labels and a custom tooltip.';

    protected string $color = 'danger';

    protected function getLayout(): TileLayout
    {
        return new ServerRoomLayout;
    }

    protected function getData(): array
    {
        return SampleData::perKey(array_keys($this->getLayout()->getTiles()), 2, 12, seed: 4);
    }

    protected function getThresholds(): array
    {
        return [4, 6, 8, 10];
    }

    protected function getLegendLessLabel(): string
    {
        return 'Cool';
    }

    protected function getLegendMoreLabel(): string
    {
        return 'Hot';
    }

    protected function getCellTooltip(HeatmapCell $cell): string
    {
        return "{$cell->getLabel()}: {$cell->getFormattedValue()} kW";
    }
}
