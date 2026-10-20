<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Pages;

use Awcodes\Thermal\Entries\UsStatesHeatmapEntry;
use Awcodes\Thermal\Layouts\UsStatesLayout;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;
use Workbench\App\Filament\Widgets\States;
use Workbench\App\Support\SampleData;

class StatesDashboard extends Dashboard
{
    protected static string $routePath = 'states';

    protected static ?string $title = 'U.S. States widgets';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?int $navigationSort = 5;

    public function getWidgets(): array
    {
        return [
            States\LeadActivity::class,
            States\StoreOpenings::class,
            States\SupportTickets::class,
        ];
    }

    public function getColumns(): int
    {
        return 3;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('campaign')
                ->label('View a campaign in a modal')
                ->modalHeading('Spring campaign')
                ->modalWidth('3xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->schema([
                    UsStatesHeatmapEntry::make('lead_distribution')
                        ->hiddenLabel()
                        ->data(fn (): array => SampleData::perKey(array_keys((new UsStatesLayout)->getTiles()), 0, 300, seed: 10))
                        ->color('info')
                        ->unit('lead'),
                ]),
        ];
    }
}
