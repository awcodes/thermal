<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Layouts;

use InvalidArgumentException;

/**
 * The geometry of a tile heatmap: which tiles exist and where each one sits. A layout
 * never holds values, colors or levels.
 *
 * Pass tiles to the constructor, or extend the class and override `tiles()`.
 */
class TileLayout
{
    /** @var array<string, Tile>|null */
    private ?array $resolved = null;

    /**
     * @param  list<Tile>  $tiles
     */
    public function __construct(private readonly array $tiles = []) {}

    /**
     * Every tile keyed by its key, in the order the layout lists them.
     *
     * @return array<string, Tile>
     */
    final public function getTiles(): array
    {
        return $this->resolved ??= $this->resolve($this->tiles());
    }

    final public function getTile(string $key): ?Tile
    {
        return $this->getTiles()[$key] ?? null;
    }

    final public function hasTile(string $key): bool
    {
        return array_key_exists($key, $this->getTiles());
    }

    /**
     * The tile key a data key refers to, or null when the layout has no such tile. Keys match
     * exactly by default; override this to accept aliases, such as lowercase abbreviations.
     */
    public function resolveKey(int | string $key): ?string
    {
        $key = (string) $key;

        return $this->hasTile($key) ? $key : null;
    }

    /**
     * The number of rows the layout spans, counted from row 1.
     */
    final public function getRows(): int
    {
        return max([0, ...array_map(fn (Tile $tile): int => $tile->getRow(), $this->getTiles())]);
    }

    /**
     * The number of columns the layout spans, counted from column 1.
     */
    final public function getColumns(): int
    {
        return max([0, ...array_map(fn (Tile $tile): int => $tile->getColumn(), $this->getTiles())]);
    }

    /**
     * @return list<Tile>
     */
    protected function tiles(): array
    {
        return $this->tiles;
    }

    /**
     * @param  array<mixed>  $tiles
     * @return array<string, Tile>
     */
    private function resolve(array $tiles): array
    {
        $resolved = [];
        $positions = [];

        foreach ($tiles as $tile) {
            if (! $tile instanceof Tile) {
                throw new InvalidArgumentException('A tile layout may only contain [' . Tile::class . '] instances.');
            }

            $key = $tile->getKey();
            $position = "{$tile->getRow()}:{$tile->getColumn()}";

            if (isset($resolved[$key])) {
                throw new InvalidArgumentException("The tile layout contains the key [{$key}] more than once.");
            }

            if (isset($positions[$position])) {
                throw new InvalidArgumentException("Tiles [{$positions[$position]}] and [{$key}] are both placed at row {$tile->getRow()}, column {$tile->getColumn()}.");
            }

            $resolved[$key] = $tile;
            $positions[$position] = $key;
        }

        return $resolved;
    }
}
