<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Tests\Fixtures;

use Awcodes\Thermal\Layouts\TileLayout;
use Awcodes\Thermal\Widgets\TileHeatmapWidget;

class RackHealthWidget extends TileHeatmapWidget
{
    public static bool $interactive = false;

    /** @var list<string> */
    public array $events = [];

    protected ?string $heading = 'Rack health';

    protected string $color = 'success';

    protected ?string $unit = 'request';

    protected function getLayout(): TileLayout
    {
        return new RackLayout;
    }

    protected function getData(): array
    {
        return ['east-1' => 83, 'east-2' => 0, 'west-1' => 1284];
    }

    protected function isInteractive(): bool
    {
        return static::$interactive;
    }

    protected function cellSelected(string $key): void
    {
        $this->events[] = "selected:{$key}";
    }

    protected function cellDeselected(string $key): void
    {
        $this->events[] = "deselected:{$key}";
    }
}
