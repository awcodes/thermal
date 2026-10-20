# Thermal

Heatmap visualizations for Filament. Display activity over time or across custom tile layouts using lightweight, theme-aware Filament components.

> [!WARNING]
> Thermal is under active development and has not been released. Its API will change.

## Features

- **Calendar** — a GitHub-style grid of daily activity, with configurable history length and week start.
- **Tile** — values on any explicitly positioned layout you define, such as racks, desks or machines.
- **U.S. States** — a built-in tile map of the 50 states and Washington, D.C.
- Every heatmap works as a dashboard **widget** and as a read-only **schema entry** for infolists, record pages and modals.
- Automatic scaling that holds up to outliers and repeated values, or custom thresholds.
- Zero and missing values stay visually and semantically distinct.
- Filament colors and dark mode, a legend, tooltips, and an accessible description of every cell.
- Opt-in, keyboard-accessible cell selection on widgets.

## Installation

```bash
composer require awcodes/thermal
```

Thermal supports Filament 4.x and 5.x on PHP 8.2 or later.

Add the package's views as a source in your Filament theme's CSS file:

```css
@source '../../../../vendor/awcodes/thermal/resources/**/*.blade.php';
```

## Quick start

```php
use App\Models\Lead;
use Awcodes\Thermal\Widgets\UsStatesHeatmapWidget;

class LeadActivity extends UsStatesHeatmapWidget
{
    protected ?string $heading = 'Lead activity across the U.S.';

    protected ?string $unit = 'lead';

    protected function getData(): array
    {
        return Lead::query()
            ->selectRaw('state, count(*) as total')
            ->groupBy('state')
            ->pluck('total', 'state')
            ->all();
    }
}
```

The same visualization is available in a schema:

```php
use Awcodes\Thermal\Entries\UsStatesHeatmapEntry;

UsStatesHeatmapEntry::make('lead_distribution')
    ->data(fn (Campaign $record): array => $record->leadsByState())
    ->unit('lead');
```

## Documentation

- [Installation](docs/installation.md)
- [Calendar](docs/calendar.md)
- [Tile](docs/tiles.md)
- [U.S. States](docs/us-states.md)
- [Data and scaling](docs/data-and-scaling.md)
- [Colors, legends and tooltips](docs/appearance.md)
- [Interaction and accessibility](docs/interaction.md)

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
