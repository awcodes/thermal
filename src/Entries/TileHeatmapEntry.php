<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Entries;

use Awcodes\Thermal\Layouts\TileLayout;
use Awcodes\Thermal\Tiles\TileHeatmap;
use Closure;
use LogicException;

class TileHeatmapEntry extends HeatmapEntry
{
    protected string $view = 'thermal::entries.tile-heatmap-entry';

    protected TileLayout | Closure | null $layout = null;

    public function layout(TileLayout | Closure | null $layout): static
    {
        $this->layout = $layout;

        return $this;
    }

    public function getLayout(): TileLayout
    {
        $layout = $this->evaluate($this->layout);

        if (! $layout instanceof TileLayout) {
            throw new LogicException("The [{$this->getName()}] tile heatmap entry needs a layout.");
        }

        return $layout;
    }

    public function getHeatmap(): TileHeatmap
    {
        return new TileHeatmap(
            data: $this->getData(),
            layout: $this->getLayout(),
            unit: $this->getUnit(),
            thresholds: $this->getThresholds(),
            tooltipUsing: $this->getCellTooltipCallback(),
            color: $this->getColor(),
            hasLegend: $this->hasLegend(),
            legendLessLabel: $this->getLegendLessLabel(),
            legendMoreLabel: $this->getLegendMoreLabel(),
            fillMissing: $this->getFillMissing(),
        );
    }
}
