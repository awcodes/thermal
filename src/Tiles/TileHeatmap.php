<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Tiles;

use Awcodes\Thermal\Layouts\Tile;
use Awcodes\Thermal\Layouts\TileLayout;
use Awcodes\Thermal\Support\HeatmapCell;
use Awcodes\Thermal\Support\HeatmapScale;
use Closure;
use InvalidArgumentException;

/**
 * Resolves values keyed by tile onto the explicit positions of a tile layout.
 */
final class TileHeatmap
{
    private readonly HeatmapScale $scale;

    /** @var array<string, int | float | null> */
    private readonly array $values;

    /** @var array<string, HeatmapCell>|null */
    private ?array $cells = null;

    /**
     * @param  array<array-key, mixed>  $data  Values keyed by tile, such as `['east-1' => 83]`.
     * @param  array<mixed>|null  $thresholds  Custom level thresholds, or null to scale automatically.
     * @param  (Closure(HeatmapCell): ?string)|null  $tooltipUsing
     * @param  int|float|null  $fillMissing  A value for tiles without data, or null to keep them missing.
     */
    public function __construct(
        array $data,
        private readonly TileLayout $layout,
        private readonly ?string $unit = null,
        ?array $thresholds = null,
        private readonly ?string $selectedKey = null,
        private readonly ?Closure $tooltipUsing = null,
        private readonly string $color = 'primary',
        private readonly bool $hasLegend = true,
        private readonly ?string $legendLessLabel = null,
        private readonly ?string $legendMoreLabel = null,
        private readonly int | float | null $fillMissing = null,
    ) {
        $this->values = $this->normalize($data);

        $this->scale = $thresholds === null
            ? HeatmapScale::fromValues($this->values)
            : HeatmapScale::fromThresholds($thresholds);
    }

    /**
     * Every resolved cell keyed by tile, in layout order.
     *
     * @return array<string, HeatmapCell>
     */
    public function getCells(): array
    {
        if ($this->cells !== null) {
            return $this->cells;
        }

        $cells = [];

        foreach ($this->layout->getTiles() as $key => $tile) {
            $cells[$key] = $this->makeCell($tile);
        }

        return $this->cells = $cells;
    }

    public function getCell(string $key): ?HeatmapCell
    {
        return $this->getCells()[$key] ?? null;
    }

    public function hasCell(string $key): bool
    {
        return $this->layout->hasTile($key);
    }

    public function getLayout(): TileLayout
    {
        return $this->layout;
    }

    public function getScale(): HeatmapScale
    {
        return $this->scale;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function hasLegend(): bool
    {
        return $this->hasLegend;
    }

    public function getLegendLessLabel(): ?string
    {
        return $this->legendLessLabel;
    }

    public function getLegendMoreLabel(): ?string
    {
        return $this->legendMoreLabel;
    }

    private function makeCell(Tile $tile): HeatmapCell
    {
        $key = $tile->getKey();
        $value = $this->values[$key] ?? $this->fillMissing;

        $cell = HeatmapCell::make($key)
            ->value($value)
            ->level($this->scale->getLevel($value))
            ->label($tile->getName())
            ->unit($this->unit)
            ->selected($key === $this->selectedKey);

        if ($this->tooltipUsing instanceof Closure && filled($tooltip = ($this->tooltipUsing)($cell))) {
            $cell->tooltip($tooltip);
        }

        return $cell;
    }

    /**
     * Keeps the values for tiles in the layout, keyed as the layout resolves them. Keys the
     * layout does not contain are left out of the heatmap and its scale, numeric strings from
     * database drivers are cast, and values for the same tile are added together.
     *
     * @param  array<array-key, mixed>  $data
     * @return array<string, int | float | null>
     */
    private function normalize(array $data): array
    {
        $values = [];

        foreach ($data as $key => $value) {
            $tileKey = $this->layout->resolveKey($key);

            if ($tileKey === null) {
                continue;
            }

            if (is_string($value) && is_numeric($value)) {
                $value += 0;
            }

            if ($value !== null && ! is_int($value) && ! is_float($value)) {
                throw new InvalidArgumentException("Tile heatmap values must be numeric or null, [{$key}] is not.");
            }

            $values[$tileKey] = $value === null
                ? ($values[$tileKey] ?? null)
                : ($values[$tileKey] ?? 0) + $value;
        }

        return $values;
    }
}
