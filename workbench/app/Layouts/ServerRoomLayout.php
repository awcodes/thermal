<?php

declare(strict_types=1);

namespace Workbench\App\Layouts;

use Awcodes\Thermal\Layouts\Tile;
use Awcodes\Thermal\Layouts\TileLayout;

/**
 * Four rows of ten racks. A cold aisle separates rows B and C, and a cross aisle
 * splits every row after the fifth rack, so the empty row and column must survive rendering.
 */
class ServerRoomLayout extends TileLayout
{
    protected function tiles(): array
    {
        $tiles = [];

        foreach (['A' => 1, 'B' => 2, 'C' => 4, 'D' => 5] as $row => $gridRow) {
            for ($rack = 1; $rack <= 10; $rack++) {
                $tiles[] = Tile::make(
                    key: strtolower($row) . '-' . $rack,
                    label: $row . $rack,
                    row: $gridRow,
                    column: $rack <= 5 ? $rack : $rack + 1,
                    name: "Rack {$row}{$rack}",
                );
            }
        }

        return $tiles;
    }
}
