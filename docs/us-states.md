---
title: U.S. States
description: Display values for the 50 states and Washington, D.C. on a tile map in a Filament widget or schema entry.
---

# U.S. States

The U.S. States heatmap is a [tile heatmap](tiles.md) with a built-in layout of the 50
states and Washington, D.C. It is built from values keyed by state:

```php
['CA' => 1284, 'TX' => 894, 'GA' => 542]
```

## Widget

Extend `UsStatesHeatmapWidget` and return your data. The layout is already provided:

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

## Entry

`UsStatesHeatmapEntry` uses the same layout inside a read-only schema:

```php
use Awcodes\Thermal\Entries\UsStatesHeatmapEntry;

UsStatesHeatmapEntry::make('lead_distribution')
    ->data(fn (Campaign $record): array => $record->leadsByState())
    ->unit('lead');
```

## State keys

Tiles are keyed by USPS abbreviation, such as `CA` and `DC`. Data keys are also matched:

- in any case and with surrounding whitespace, so `ca` and ` Ca ` both mean California;
- by full name in any case, such as `California` or `new hampshire`;
- as `District of Columbia` or `Washington DC` for Washington, D.C.

Values that resolve to the same state are added together. Keys that are not a state or
D.C., including U.S. territories such as `PR`, are ignored.

Each tile shows the abbreviation, and its tooltip and accessible description use the full
name, such as `California — 1,284 leads`.

## The layout

The states sit on an 11-column, 8-row square tile grid, the widely used tile map
convention. Every state keeps its rough neighbors, Alaska and Hawaii sit at the top and
bottom of the western edge, and Washington, D.C. sits directly below Maryland.

The layout is available on its own as `Awcodes\Thermal\Layouts\UsStatesLayout`, for
example to pass to a `TileHeatmapEntry` or to extend.
