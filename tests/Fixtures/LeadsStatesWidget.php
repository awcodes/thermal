<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Tests\Fixtures;

use Awcodes\Thermal\Widgets\UsStatesHeatmapWidget;

class LeadsStatesWidget extends UsStatesHeatmapWidget
{
    public static bool $interactive = false;

    /** @var list<string> */
    public array $events = [];

    protected ?string $heading = 'Lead activity across the U.S.';

    protected ?string $unit = 'lead';

    protected function getData(): array
    {
        return ['CA' => 1284, 'tx' => 894, 'Georgia' => 542, 'WY' => 0];
    }

    protected function isInteractive(): bool
    {
        return static::$interactive;
    }

    protected function cellSelected(string $key): void
    {
        $this->events[] = "selected:{$key}";
    }
}
