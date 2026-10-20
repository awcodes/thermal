<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Tests\Fixtures;

use Awcodes\Thermal\Entries\CalendarHeatmapEntry;
use Carbon\CarbonImmutable;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CustomerInfolist extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state(['activity' => ['2026-10-06' => 42, '2026-10-19' => 0]])
            ->components([
                CalendarHeatmapEntry::make('activity')
                    ->weeks(4)
                    ->endDate(CarbonImmutable::parse('2026-10-20'))
                    ->unit('order')
                    ->color('success'),
            ]);
    }

    public function render(): View
    {
        return view()->file(__DIR__ . '/schema.blade.php');
    }
}
