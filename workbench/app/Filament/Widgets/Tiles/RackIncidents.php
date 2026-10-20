<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\Tiles;

use Awcodes\Thermal\Layouts\TileLayout;
use Awcodes\Thermal\Widgets\TileHeatmapWidget;
use Workbench\App\Layouts\ServerRoomLayout;
use Workbench\App\Support\SampleData;

class RackIncidents extends TileHeatmapWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Incidents';

    protected string $color = 'warning';

    protected ?string $unit = 'incident';

    protected int | string | array $columnSpan = 2;

    public function getDescription(): string
    {
        $tile = $this->selectedCell === null ? null : $this->getLayout()->getTile($this->selectedCell);

        if (! $tile instanceof \Awcodes\Thermal\Layouts\Tile) {
            return 'Sparse data: most racks report zero (solid gray) and offline racks report nothing (outlined). Interactive: select a rack.';
        }

        return "Selected {$tile->getName()}. Select it again to clear.";
    }

    protected function getLayout(): TileLayout
    {
        return new ServerRoomLayout;
    }

    protected function getData(): array
    {
        $data = array_map(
            fn (int $value): int => $value > 340 ? intdiv($value, 40) : 0,
            SampleData::perKey(array_keys($this->getLayout()->getTiles()), seed: 2),
        );

        unset($data['b-3'], $data['c-8'], $data['d-9']);

        return $data;
    }

    protected function isInteractive(): bool
    {
        return true;
    }
}
