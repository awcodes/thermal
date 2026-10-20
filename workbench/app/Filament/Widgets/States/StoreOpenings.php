<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\States;

use Awcodes\Thermal\Layouts\UsStatesLayout;
use Awcodes\Thermal\Widgets\UsStatesHeatmapWidget;

class StoreOpenings extends UsStatesHeatmapWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Store openings';

    protected string $color = 'success';

    protected ?string $unit = 'store';

    protected int | string | array $columnSpan = 2;

    public function getDescription(): string
    {
        $tile = $this->selectedCell === null ? null : (new UsStatesLayout)->getTile($this->selectedCell);

        if (! $tile instanceof \Awcodes\Thermal\Layouts\Tile) {
            return 'Sparse data keyed by lowercase abbreviations and full names. Zero states are solid gray, states without data are outlined. Interactive: select a state.';
        }

        return "Selected {$tile->getName()}. Select it again to clear.";
    }

    protected function getData(): array
    {
        return [
            'ca' => 12,
            'tx' => 9,
            'New York' => 7,
            'fl' => 6,
            'Georgia' => 3,
            'wa' => 2,
            'District of Columbia' => 1,
            'ma' => 1,
            'nv' => 0,
            'Oregon' => 0,
            'az' => 0,
        ];
    }

    protected function isInteractive(): bool
    {
        return true;
    }
}
