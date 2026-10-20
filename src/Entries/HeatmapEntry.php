<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Entries;

use Awcodes\Thermal\Support\HeatmapCell;
use Closure;
use Filament\Infolists\Components\Entry;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

/**
 * Read-only schema configuration shared by every heatmap entry.
 */
abstract class HeatmapEntry extends Entry
{
    /** @var array<string, mixed> | Closure | null */
    protected array | Closure | null $data = null;

    protected string | Closure $color = 'primary';

    protected string | Closure | null $unit = null;

    /** @var array<mixed> | Closure | null */
    protected array | Closure | null $thresholds = null;

    protected bool | Closure $hasLegend = true;

    protected int | float | Closure | null $fillMissing = null;

    protected string | Closure | null $legendLessLabel = null;

    protected string | Closure | null $legendMoreLabel = null;

    protected ?Closure $cellTooltipUsing = null;

    /**
     * Values keyed by cell. Without this, the entry's state is used.
     *
     * @param  array<string, mixed> | Closure | null  $data
     */
    public function data(array | Closure | null $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * A Filament color name, such as `primary` or `success`.
     */
    public function color(string | Closure $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * The singular unit counted by each value, such as `order`.
     */
    public function unit(string | Closure | null $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Custom level thresholds, such as `[100, 250, 500, 1000]`, or null to scale automatically.
     *
     * @param  array<mixed> | Closure | null  $thresholds
     */
    public function thresholds(array | Closure | null $thresholds): static
    {
        $this->thresholds = $thresholds;

        return $this;
    }

    /**
     * A value for cells without data, such as `0` when a query leaves out days with no activity.
     * Null keeps those cells missing.
     */
    public function fillMissing(int | float | Closure | null $value): static
    {
        $this->fillMissing = $value;

        return $this;
    }

    public function legend(bool | Closure $condition = true): static
    {
        $this->hasLegend = $condition;

        return $this;
    }

    public function legendLabels(string | Closure | null $less, string | Closure | null $more): static
    {
        $this->legendLessLabel = $less;
        $this->legendMoreLabel = $more;

        return $this;
    }

    /**
     * Receives each `HeatmapCell` as `$cell` and returns a replacement tooltip, or null to keep the default.
     */
    public function cellTooltip(?Closure $callback): static
    {
        $this->cellTooltipUsing = $callback;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        $data = $this->evaluate($this->data) ?? $this->getState() ?? [];

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        if (! is_array($data)) {
            throw new InvalidArgumentException("The [{$this->getName()}] heatmap entry needs its data as an array.");
        }

        return $data;
    }

    public function getColor(): string
    {
        return $this->evaluate($this->color);
    }

    public function getUnit(): ?string
    {
        return $this->evaluate($this->unit);
    }

    /**
     * @return array<mixed>|null
     */
    public function getThresholds(): ?array
    {
        return $this->evaluate($this->thresholds);
    }

    public function getFillMissing(): int | float | null
    {
        return $this->evaluate($this->fillMissing);
    }

    public function hasLegend(): bool
    {
        return (bool) $this->evaluate($this->hasLegend);
    }

    public function getLegendLessLabel(): ?string
    {
        return $this->evaluate($this->legendLessLabel);
    }

    public function getLegendMoreLabel(): ?string
    {
        return $this->evaluate($this->legendMoreLabel);
    }

    /**
     * @return Closure(HeatmapCell): ?string
     */
    protected function getCellTooltipCallback(): Closure
    {
        return fn (HeatmapCell $cell): ?string => $this->cellTooltipUsing instanceof Closure
            ? $this->evaluate($this->cellTooltipUsing, ['cell' => $cell], [HeatmapCell::class => $cell])
            : null;
    }
}
