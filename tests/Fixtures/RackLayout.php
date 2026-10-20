<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Tests\Fixtures;

use Awcodes\Thermal\Layouts\Tile;
use Awcodes\Thermal\Layouts\TileLayout;

/**
 * Two racks side by side with an empty aisle column between them.
 */
class RackLayout extends TileLayout
{
    protected function tiles(): array
    {
        return [
            Tile::make('east-1', 'E1', row: 1, column: 1, name: 'East 1'),
            Tile::make('east-2', 'E2', row: 2, column: 1, name: 'East 2'),
            Tile::make('west-1', 'W1', row: 1, column: 3, name: 'West 1'),
            Tile::make('west-2', 'W2', row: 2, column: 3, name: 'West 2'),
        ];
    }
}
