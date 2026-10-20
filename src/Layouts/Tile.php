<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Layouts;

use InvalidArgumentException;

/**
 * Where one cell of a tile layout sits, and what it is called. Rows and columns start at 1.
 */
final readonly class Tile
{
    public function __construct(
        private string $key,
        private string $label,
        private int $row,
        private int $column,
        private ?string $name = null,
    ) {
        if ($key === '') {
            throw new InvalidArgumentException('A tile needs a key.');
        }

        if ($row < 1 || $column < 1) {
            throw new InvalidArgumentException("Tile [{$key}] must be placed at row 1 and column 1 or later.");
        }
    }

    /**
     * @param  string  $label  The short label drawn on the tile, such as `CA`.
     * @param  string|null  $name  The full name used for tooltips and screen readers, such as `California`.
     */
    public static function make(string $key, string $label, int $row, int $column, ?string $name = null): self
    {
        return new self($key, $label, $row, $column, $name);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getName(): string
    {
        return $this->name ?? $this->label;
    }

    public function getRow(): int
    {
        return $this->row;
    }

    public function getColumn(): int
    {
        return $this->column;
    }
}
