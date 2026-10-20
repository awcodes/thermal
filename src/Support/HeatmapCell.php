<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Support;

use Illuminate\Support\Number;
use Illuminate\Support\Str;

/**
 * A single resolved heatmap cell: what it means, not where it is drawn.
 */
class HeatmapCell
{
    protected int | float | null $value = null;

    protected int $level = 0;

    protected ?string $label = null;

    protected ?string $unit = null;

    protected ?string $tooltip = null;

    protected bool $isSelected = false;

    final public function __construct(protected string $key) {}

    public static function make(string $key): static
    {
        return new static($key);
    }

    public function value(int | float | null $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function level(int $level): static
    {
        $this->level = max(0, min(HeatmapScale::MAX_LEVEL, $level));

        return $this;
    }

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * The singular unit counted by the value, such as `order`. It is pluralized to match the value.
     */
    public function unit(?string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function tooltip(?string $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function selected(bool $condition = true): static
    {
        $this->isSelected = $condition;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): int | float | null
    {
        return $this->value;
    }

    public function hasValue(): bool
    {
        return $this->value !== null;
    }

    public function getLevel(): int
    {
        return $this->hasValue() ? $this->level : 0;
    }

    public function getLabel(): string
    {
        return $this->label ?? $this->key;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function isSelected(): bool
    {
        return $this->isSelected;
    }

    /**
     * The value with its unit, such as `1,284 leads`, or null when the cell has no data.
     */
    public function getFormattedValue(): ?string
    {
        if ($this->value === null) {
            return null;
        }

        $number = (string) Number::format($this->value, maxPrecision: 2);

        if (blank($this->unit)) {
            return $number;
        }

        return $number . ' ' . Str::plural($this->unit, $this->value === 1 ? 1 : 2);
    }

    public function getTooltip(): string
    {
        return $this->tooltip ?? $this->getLabel() . ' — ' . ($this->getFormattedValue() ?? __('thermal::heatmap.missing'));
    }

    /**
     * The accessible equivalent of the cell's color and tooltip, such as
     * `California, 1,284 leads, high activity`.
     */
    public function getDescription(): string
    {
        if (! $this->hasValue()) {
            return $this->getLabel() . ', ' . __('thermal::heatmap.missing');
        }

        return implode(', ', [
            $this->getLabel(),
            $this->getFormattedValue(),
            __('thermal::heatmap.levels.' . $this->getLevel()),
        ]);
    }
}
