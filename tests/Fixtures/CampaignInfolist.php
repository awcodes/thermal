<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Tests\Fixtures;

use Awcodes\Thermal\Entries\UsStatesHeatmapEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CampaignInfolist extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state(['lead_distribution' => ['CA' => 1284, 'tx' => 894, 'Georgia' => 542, 'WY' => 0]])
            ->components([
                UsStatesHeatmapEntry::make('lead_distribution')
                    ->unit('lead'),
            ]);
    }

    public function render(): View
    {
        return view()->file(__DIR__ . '/schema.blade.php');
    }
}
