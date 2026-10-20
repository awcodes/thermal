<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\States;

use Awcodes\Thermal\Layouts\UsStatesLayout;
use Awcodes\Thermal\Widgets\UsStatesHeatmapWidget;
use Workbench\App\Support\SampleData;

class SupportTickets extends UsStatesHeatmapWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Support tickets';

    protected ?string $description = 'Narrow: tiles shrink, then scroll. California is a large outlier.';

    protected string $color = 'danger';

    protected ?string $unit = 'ticket';

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        return [
            ...SampleData::perKey(array_keys((new UsStatesLayout)->getTiles()), 5, 60, seed: 9),
            'CA' => 40_000,
        ];
    }
}
