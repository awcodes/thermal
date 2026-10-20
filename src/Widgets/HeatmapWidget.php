<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Widgets;

use Awcodes\Thermal\Support\HeatmapCell;
use Filament\Widgets\Widget;

/**
 * Widget chrome and opt-in cell selection shared by every heatmap widget.
 */
abstract class HeatmapWidget extends Widget
{
    public ?string $selectedCell = null;

    protected ?string $heading = null;

    protected ?string $description = null;

    /**
     * A Filament color name, such as `primary` or `success`.
     */
    protected string $color = 'primary';

    /**
     * The singular unit counted by each value, such as `order`.
     */
    protected ?string $unit = null;

    protected bool $hasLegend = true;

    /**
     * A value for cells without data, such as `0` when a query leaves out days with no activity.
     * Null keeps those cells missing.
     */
    protected int | float | null $fillMissing = null;

    protected int | string | array $columnSpan = 'full';

    /**
     * @return array<string, mixed>
     */
    abstract protected function getData(): array;

    abstract protected function hasCell(string $key): bool;

    public function getHeading(): ?string
    {
        return $this->heading;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Toggles the selection of a cell. Unknown keys, and every key when the widget is not
     * interactive, are ignored, because the key comes from the browser.
     */
    public function selectCell(string $key): void
    {
        if (! $this->isInteractive() || ! $this->hasCell($key)) {
            return;
        }

        if ($this->selectedCell === $key) {
            $this->selectedCell = null;
            $this->cellDeselected($key);

            return;
        }

        $this->selectedCell = $key;
        $this->cellSelected($key);
    }

    protected function getColor(): string
    {
        return $this->color;
    }

    protected function getUnit(): ?string
    {
        return $this->unit;
    }

    /**
     * Custom level thresholds, such as `[100, 250, 500, 1000]`, or null to scale automatically.
     *
     * @return array<mixed>|null
     */
    protected function getThresholds(): ?array
    {
        return null;
    }

    protected function getFillMissing(): int | float | null
    {
        return $this->fillMissing;
    }

    protected function hasLegend(): bool
    {
        return $this->hasLegend;
    }

    protected function getLegendLessLabel(): ?string
    {
        return null;
    }

    protected function getLegendMoreLabel(): ?string
    {
        return null;
    }

    /**
     * Return a string to replace a cell's default tooltip, or null to keep it.
     */
    protected function getCellTooltip(HeatmapCell $cell): ?string
    {
        return null;
    }

    protected function isInteractive(): bool
    {
        return false;
    }

    protected function cellSelected(string $key): void {}

    protected function cellDeselected(string $key): void {}
}
