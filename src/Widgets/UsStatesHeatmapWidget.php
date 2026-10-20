<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Widgets;

use Awcodes\Thermal\Layouts\TileLayout;
use Awcodes\Thermal\Layouts\UsStatesLayout;

abstract class UsStatesHeatmapWidget extends TileHeatmapWidget
{
    protected function getLayout(): TileLayout
    {
        return new UsStatesLayout;
    }
}
