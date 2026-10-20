<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\States;

use Awcodes\Thermal\Layouts\UsStatesLayout;
use Awcodes\Thermal\Widgets\UsStatesHeatmapWidget;
use Workbench\App\Support\SampleData;

class LeadActivity extends UsStatesHeatmapWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Lead activity across the U.S.';

    protected ?string $description = 'A normal distribution across all 50 states and D.C.';

    protected ?string $unit = 'lead';

    protected function getData(): array
    {
        return SampleData::perKey(array_keys((new UsStatesLayout)->getTiles()), 50, 1500);
    }
}
