<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Widgets;

use Awcodes\Thermal\Layouts\TileLayout;
use Awcodes\Thermal\Tiles\TileHeatmap;

abstract class TileHeatmapWidget extends HeatmapWidget
{
    protected string $view = 'thermal::widgets.tile-heatmap-widget';

    abstract protected function getLayout(): TileLayout;

    public function getHeatmap(): TileHeatmap
    {
        return new TileHeatmap(
            data: $this->getData(),
            layout: $this->getLayout(),
            unit: $this->getUnit(),
            thresholds: $this->getThresholds(),
            selectedKey: $this->selectedCell,
            tooltipUsing: $this->getCellTooltip(...),
            color: $this->getColor(),
            hasLegend: $this->hasLegend(),
            legendLessLabel: $this->getLegendLessLabel(),
            legendMoreLabel: $this->getLegendMoreLabel(),
            fillMissing: $this->getFillMissing(),
        );
    }

    protected function hasCell(string $key): bool
    {
        return $this->getLayout()->hasTile($key);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'heatmap' => $this->getHeatmap(),
            'interactive' => $this->isInteractive(),
        ];
    }
}
