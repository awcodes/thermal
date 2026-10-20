<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Entries;

use Awcodes\Thermal\Layouts\UsStatesLayout;

class UsStatesHeatmapEntry extends TileHeatmapEntry
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->layout(new UsStatesLayout);
    }
}
