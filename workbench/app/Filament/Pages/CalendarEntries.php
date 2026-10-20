<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Pages;

use Awcodes\Thermal\Entries\CalendarHeatmapEntry;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Workbench\App\Support\SampleData;

class CalendarEntries extends Page
{
    protected static ?string $title = 'Calendar entries';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?int $navigationSort = 2;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->state([
                'orders' => SampleData::daily(),
                'logins' => SampleData::sparse(seed: 7),
            ])
            ->components([
                Section::make('Customer activity')
                    ->description('Calendar entries in a read-only schema, as on a record view page.')
                    ->extraAttributes(['data-focus' => 'calendar-entries'])
                    ->schema([
                        CalendarHeatmapEntry::make('orders')
                            ->endDate(SampleData::today())
                            ->label('Orders over the last year')
                            ->color('success')
                            ->unit('order'),
                        Grid::make(2)->schema([
                            CalendarHeatmapEntry::make('logins')
                                ->endDate(SampleData::today())
                                ->label('Logins, last 12 weeks (missing days filled with 0)')
                                ->weeks(12)
                                ->weekStartsOn(1)
                                ->color('info')
                                ->unit('login')
                                ->legendLabels('Quiet', 'Busy')
                                ->fillMissing(0),
                            CalendarHeatmapEntry::make('reviews')
                                ->endDate(SampleData::today())
                                ->label('Reviews, last 12 weeks (data closure)')
                                ->data(fn (): array => SampleData::daily(84, seed: 8))
                                ->weeks(12)
                                ->color('warning')
                                ->unit('review'),
                        ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('activity')
                ->label('View activity in a modal')
                ->modalHeading('Customer activity')
                ->modalWidth('4xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->schema([
                    CalendarHeatmapEntry::make('activity')
                        ->endDate(SampleData::today())
                        ->hiddenLabel()
                        ->data(fn (): array => SampleData::daily(seed: 9))
                        ->color('primary')
                        ->unit('event'),
                ]),
        ];
    }
}
