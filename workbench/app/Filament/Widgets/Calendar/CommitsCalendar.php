<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Widgets\Calendar;

use Awcodes\Thermal\Support\HeatmapCell;
use Awcodes\Thermal\Widgets\CalendarHeatmapWidget;
use Carbon\CarbonImmutable;
use Workbench\App\Support\SampleData;

class CommitsCalendar extends CalendarHeatmapWidget
{
    protected static ?int $sort = 6;

    protected ?string $heading = 'Commits';

    protected int $weeks = 26;

    protected int $weekStartsOn = 1;

    protected ?string $unit = 'commit';

    public function getDescription(): string
    {
        if ($this->selectedCell === null) {
            return 'Interactive with a custom tooltip. Select a day with the mouse or keyboard.';
        }

        return 'Selected ' . CarbonImmutable::parse($this->selectedCell)->format('l, F j, Y') . '. Select it again to clear.';
    }

    protected function getData(): array
    {
        return SampleData::daily(seed: 6);
    }

    protected function isInteractive(): bool
    {
        return true;
    }

    protected function getCellTooltip(HeatmapCell $cell): ?string
    {
        return $cell->hasValue() ? "{$cell->getFormattedValue()} — click to select" : null;
    }
}
