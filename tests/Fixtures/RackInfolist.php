<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Tests\Fixtures;

use Awcodes\Thermal\Entries\TileHeatmapEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class RackInfolist extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state(['racks' => ['east-1' => 83, 'east-2' => 0, 'west-1' => 1284]])
            ->components([
                TileHeatmapEntry::make('racks')
                    ->layout(new RackLayout)
                    ->unit('request')
                    ->color('success'),
            ]);
    }

    public function render(): View
    {
        return view()->file(__DIR__ . '/schema.blade.php');
    }
}
