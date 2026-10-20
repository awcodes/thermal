<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Pages;

use Awcodes\Thermal\Entries\TileHeatmapEntry;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Workbench\App\Layouts\ServerRoomLayout;
use Workbench\App\Support\SampleData;

class TileEntries extends Page
{
    protected static ?string $title = 'Tile entries';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?int $navigationSort = 4;

    public function content(Schema $schema): Schema
    {
        $racks = array_keys((new ServerRoomLayout)->getTiles());

        return $schema
            ->state([
                'utilization' => SampleData::perKey($racks, 5, 100, seed: 5),
                'alerts' => array_slice(SampleData::perKey($racks, 1, 6, seed: 6), 0, 12),
            ])
            ->components([
                Section::make('Data center overview')
                    ->description('Tile entries in a read-only schema, as on a record view page.')
                    ->schema([
                        TileHeatmapEntry::make('utilization')
                            ->label('CPU utilization by rack')
                            ->layout(new ServerRoomLayout)
                            ->color('primary')
                            ->cellTooltip(fn ($cell): string => "{$cell->getLabel()}: {$cell->getFormattedValue()}% CPU"),
                        Grid::make(2)->schema([
                            TileHeatmapEntry::make('alerts')
                                ->label('Alerts (racks without data filled with 0)')
                                ->layout(new ServerRoomLayout)
                                ->color('warning')
                                ->unit('alert')
                                ->fillMissing(0),
                            TileHeatmapEntry::make('maintenance')
                                ->label('Maintenance hours (data closure, no legend)')
                                ->layout(fn (): ServerRoomLayout => new ServerRoomLayout)
                                ->data(fn (): array => SampleData::perKey($racks, 0, 12, seed: 7))
                                ->color('gray')
                                ->unit('hour')
                                ->legend(false),
                        ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('racks')
                ->label('View racks in a modal')
                ->modalHeading('Rack utilization')
                ->modalWidth('3xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->schema([
                    TileHeatmapEntry::make('racks')
                        ->hiddenLabel()
                        ->layout(new ServerRoomLayout)
                        ->data(fn (): array => SampleData::perKey(array_keys((new ServerRoomLayout)->getTiles()), seed: 8))
                        ->color('success')
                        ->unit('request'),
                ]),
        ];
    }
}
