<?php

declare(strict_types=1);

use Awcodes\Thermal\Layouts\Tile;
use Awcodes\Thermal\Layouts\UsStatesLayout;

const STATE_KEYS = [
    'AK', 'AL', 'AR', 'AZ', 'CA', 'CO', 'CT', 'DC', 'DE', 'FL', 'GA', 'HI', 'IA', 'ID', 'IL', 'IN', 'KS',
    'KY', 'LA', 'MA', 'MD', 'ME', 'MI', 'MN', 'MO', 'MS', 'MT', 'NC', 'ND', 'NE', 'NH', 'NJ', 'NM', 'NV',
    'NY', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VA', 'VT', 'WA', 'WI', 'WV', 'WY',
];

function stateAt(int $row, int $column): ?string
{
    foreach ((new UsStatesLayout)->getTiles() as $key => $tile) {
        if ($tile->getRow() === $row && $tile->getColumn() === $column) {
            return $key;
        }
    }

    return null;
}

it('contains exactly the 50 states and Washington, D.C.', function () {
    $keys = array_keys((new UsStatesLayout)->getTiles());
    sort($keys);

    expect($keys)->toBe(STATE_KEYS);
});

it('places every tile once, inside an 11 by 8 grid', function () {
    $layout = new UsStatesLayout;
    $positions = array_map(fn (Tile $tile): string => "{$tile->getRow()}:{$tile->getColumn()}", $layout->getTiles());

    expect(array_unique($positions))->toHaveCount(51)
        ->and($layout->getColumns())->toBe(11)
        ->and($layout->getRows())->toBe(8);
});

it('labels tiles by abbreviation and names them in full', function (string $key, string $name) {
    $tile = (new UsStatesLayout)->getTile($key);

    expect($tile?->getLabel())->toBe($key)
        ->and($tile?->getName())->toBe($name);
})->with([
    ['CA', 'California'],
    ['NH', 'New Hampshire'],
    ['WV', 'West Virginia'],
    ['DC', 'Washington, D.C.'],
]);

it('keeps Alaska, Hawaii and D.C. where readers expect them', function () {
    expect(stateAt(1, 1))->toBe('AK')
        ->and(stateAt(8, 1))->toBe('HI')
        ->and(stateAt(6, 9))->toBe('DC')
        ->and(stateAt(5, 9))->toBe('MD');
});

it('keeps the Northeast in a readable order', function () {
    // Maine above New Hampshire, Vermont beside it, then Massachusetts, Connecticut and Rhode Island.
    expect([stateAt(1, 11), stateAt(2, 10), stateAt(2, 11)])->toBe(['ME', 'VT', 'NH'])
        ->and([stateAt(3, 9), stateAt(3, 10)])->toBe(['NY', 'MA'])
        ->and([stateAt(4, 8), stateAt(4, 9), stateAt(4, 10), stateAt(4, 11)])->toBe(['PA', 'NJ', 'CT', 'RI'])
        ->and([stateAt(5, 9), stateAt(5, 10)])->toBe(['MD', 'DE']);
});

it('keeps the west coast in order', function () {
    expect([stateAt(3, 1), stateAt(4, 1), stateAt(5, 1)])->toBe(['WA', 'OR', 'CA']);
});

it('resolves abbreviations in any case and full names', function (string $key, ?string $expected) {
    expect((new UsStatesLayout)->resolveKey($key))->toBe($expected);
})->with([
    ['CA', 'CA'],
    ['ca', 'CA'],
    [' Ca ', 'CA'],
    ['California', 'CA'],
    ['new hampshire', 'NH'],
    ['District of Columbia', 'DC'],
    ['Washington, D.C.', 'DC'],
    ['Washington', 'WA'],
    ['PR', null],
    ['Atlantis', null],
]);
