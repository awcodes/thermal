<?php

declare(strict_types=1);

use Awcodes\Thermal\Layouts\Tile;
use Awcodes\Thermal\Layouts\TileLayout;
use Awcodes\Thermal\Tests\Fixtures\RackLayout;

it('keys tiles in layout order and measures the grid', function () {
    $layout = new RackLayout;

    expect(array_keys($layout->getTiles()))->toBe(['east-1', 'east-2', 'west-1', 'west-2'])
        ->and($layout->getRows())->toBe(2)
        ->and($layout->getColumns())->toBe(3)
        ->and($layout->hasTile('east-1'))->toBeTrue()
        ->and($layout->hasTile('north-1'))->toBeFalse()
        ->and($layout->getTile('west-2')?->getColumn())->toBe(3);
});

it('accepts tiles through the constructor', function () {
    $layout = new TileLayout([Tile::make('a', 'A', row: 2, column: 4)]);

    expect($layout->getRows())->toBe(2)
        ->and($layout->getColumns())->toBe(4);
});

it('is empty without tiles', function () {
    $layout = new TileLayout;

    expect($layout->getTiles())->toBe([])
        ->and($layout->getRows())->toBe(0)
        ->and($layout->getColumns())->toBe(0);
});

it('falls back to the short label when a tile has no name', function () {
    expect(Tile::make('e1', 'E1', 1, 1)->getName())->toBe('E1')
        ->and(Tile::make('e1', 'E1', 1, 1, 'East 1')->getName())->toBe('East 1');
});

it('rejects duplicate keys and coordinate collisions', function (array $tiles, string $message) {
    expect(fn () => (new TileLayout($tiles))->getTiles())
        ->toThrow(InvalidArgumentException::class, $message);
})->with([
    'duplicate key' => [[Tile::make('a', 'A', 1, 1), Tile::make('a', 'A', 1, 2)], 'contains the key [a] more than once'],
    'collision' => [[Tile::make('a', 'A', 1, 1), Tile::make('b', 'B', 1, 1)], 'Tiles [a] and [b] are both placed at row 1, column 1'],
]);

it('rejects tiles without a key or outside the grid', function (Closure $make) {
    $make();
})->with([
    'empty key' => [fn () => Tile::make('', 'A', 1, 1)],
    'row zero' => [fn () => Tile::make('a', 'A', 0, 1)],
    'negative column' => [fn () => Tile::make('a', 'A', 1, -1)],
])->throws(InvalidArgumentException::class);
