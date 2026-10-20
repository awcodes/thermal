<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Tests\Fixtures;

use Awcodes\Thermal\Widgets\CalendarHeatmapWidget;
use Carbon\CarbonImmutable;

class OrdersCalendarWidget extends CalendarHeatmapWidget
{
    public static bool $interactive = false;

    /** @var list<string> */
    public array $events = [];

    protected ?string $heading = 'Orders';

    protected string $color = 'success';

    protected int $weeks = 4;

    protected ?string $unit = 'order';

    protected function getData(): array
    {
        return ['2026-10-06' => 42, '2026-10-19' => 0];
    }

    protected function getEndDate(): CarbonImmutable
    {
        return CarbonImmutable::parse('2026-10-20');
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
