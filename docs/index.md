---
title: Thermal
description: Heatmap visualizations for Filament, covering calendar activity grids, custom tile layouts and the United States.
---

# Thermal

Thermal provides heatmap visualizations for Filament. It displays activity over time or
across custom tile layouts using lightweight, theme-aware Filament components.

> [!WARNING]
> Thermal is under active development and has not been released. Its API will change.

## Visualizations

Thermal ships three heatmaps. Each one is available as a dashboard widget and as a
read-only schema entry for infolists, record pages and action modals.

- **Calendar** — a GitHub-style activity grid with one column per week and one row per
  weekday, built from values keyed by date.
- **Tile** — explicitly positioned tiles on a grid you define, such as racks in a server
  room or desks on a floor plan, built from values keyed by tile.
- **U.S. States** — a ready-made tile layout of the 50 states and Washington, D.C., built
  from values keyed by state.

All three share the same behavior: automatic or custom scaling into five intensity
levels, Filament colors with dark mode support, a legend, tooltips, and an accessible
description of every cell. Widgets can also opt in to cell selection.

## A first heatmap

A calendar widget needs a single method that returns values keyed by date:

```php
use App\Models\Order;
use Awcodes\Thermal\Widgets\CalendarHeatmapWidget;

class OrdersHeatmap extends CalendarHeatmapWidget
{
    protected ?string $heading = 'Orders';

    protected string $color = 'success';

    protected ?string $unit = 'order';

    protected function getData(): array
    {
        return Order::query()
            ->selectRaw('DATE(created_at) as date, count(*) as total')
            ->where('created_at', '>=', now()->subWeeks(52))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->all();
    }
}
```

Thermal widgets are ordinary Filament widgets, so they are displayed the same way: register
them with the panel through `discoverWidgets()` or `widgets()` to show them on the
dashboard, or return them from a page's `getWidgets()` or `getHeaderWidgets()`.

> [!IMPORTANT]
> On Filament 4, a widget that is only returned from a page and never registered with the
> panel renders once, then fails with a 419 "Page Expired" on its first Livewire request,
> such as lazy loading or selecting a cell. Livewire 3 cannot find a component that was
> never registered. Register these widgets with Livewire without adding them to the
> dashboard by using `discoverLivewireComponents()` or `livewireComponents()` on the panel:
>
> ```php
> $panel->discoverLivewireComponents(
>     in: app_path('Filament/Heatmaps'),
>     for: 'App\\Filament\\Heatmaps',
> );
> ```
>
> Filament 5 does not need this step.

## Next steps

- [Installation](installation.md) — install the package and register its views with your theme.
- [Calendar](calendar.md) — activity over time, as a widget or an entry.
- [Tile](tiles.md) — define your own tile layout.
- [U.S. States](us-states.md) — the built-in United States layout.
- [Data and scaling](data-and-scaling.md) — data formats, zero versus missing values, and how values become colors.
- [Colors, legends and tooltips](appearance.md) — theming and responsive behavior.
- [Interaction and accessibility](interaction.md) — selectable cells and how heatmaps are exposed to assistive technology.
