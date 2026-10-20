# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Thermal (`awcodes/thermal`, namespace `Awcodes\Thermal`) is a Filament 4/5 package of heatmap visualizations: a GitHub-style **Calendar**, a generic explicitly positioned **Tile** heatmap, and a **U.S. States** tile layout. Each is offered as a dashboard Widget and as a read-only schema Entry. It is unreleased; breaking changes are fine.

## Commands

```bash
composer test           # rector --dry-run, pint --test, phpstan (level 5), pest — run before every commit
composer test:unit      # pest only
composer lint           # pint (fix)
composer refactor       # rector (fix); run pint again afterwards, rector output is not pint-clean
composer build          # rebuild the Workbench (sqlite db, seed, npm ci, theme CSS)
composer serve          # build, then serve the Workbench at /admin (test@example.com / password)
composer focus          # regenerate docs/assets screenshots and art/ share cards from focus.php (needs a built Workbench)
npm run build           # recompile the Workbench theme CSS
```

Single test: `vendor/bin/pest tests/Tiles/TileHeatmapTest.php` or `vendor/bin/pest --filter "keeps zero distinct"`.

Gotchas:

- `post-autoload-dump` runs `testbench package:purge-skeleton`, so any `composer install/require/dump-autoload` wipes the Workbench database. Run `composer build` again before serving or running Focus.
- Cell styling is Tailwind classes in package Blade views, compiled by the Workbench theme (`workbench/resources/css/filament/admin/theme.css` `@source`s `resources/**`). After changing classes in views, run `npm run build`. Classes must stay literal strings, never concatenated.
- The package supports PHP `^8.2`: no typed class constants or other 8.3+ syntax in `src/`.

## Architecture

The rule throughout: **PHP resolves what cells mean; Blade lays them out; JavaScript only does keyboard navigation.**

Layers, bottom up:

1. **`Support/`** — visualization-agnostic primitives. `HeatmapCell` is a resolved cell (key, value or null, level 0–4, label, unit, tooltip, selected) with no geometry; `getDescription()` is the accessible text that stands in for color and tooltip. `HeatmapScale` maps values to levels: `fromValues()` (default) ranks *distinct* positive values into quartiles so outliers and duplicates don't flatten the scale; `fromThresholds()` takes each level's minimum. `HeatmapColor` emits the root CSS variables (`--color-*` from a Filament color plus per-level `--thermal-text-*` label colors picked for 4.5:1 contrast).
2. **Resolvers** — `Calendar/CalendarHeatmap` (dates → week columns, month/weekday labels) and `Tiles/TileHeatmap` (data joined to a `Layouts/TileLayout`). They take data plus presentation settings as constructor arguments and own positioning; normalization (numeric strings cast, unknown keys dropped before scaling) happens here.
3. **Adapters** — `Widgets/HeatmapWidget` and `Entries/HeatmapEntry` hold the shared settings; the concrete Calendar/Tile/UsStates subclasses only build their resolver in `getHeatmap()`. Widgets and Entries must not duplicate interpretation: both build the same resolver, and presentation tests assert they produce identical cells.
4. **Blade** — `components/calendar` and `components/tiles` render a resolver; `components/cell`, `legend` and `grid` are shared. `grid` holds the Alpine roving-tabindex keyboard handler used only when interactive. Widget and entry views are thin wrappers that pass the resolver and an accessible label.

Things that span files and are easy to break:

- **Zero vs missing** stay distinct everywhere: zero is level 0 drawn solid gray, null/absent is drawn outlined with "no data". `fillMissing` is the opt-in to treat absent keys as a value.
- **Level shades** are defined twice and must match: background classes in `components/cell.blade.php` and `HeatmapColor::BACKGROUNDS`, which chooses label text against those shades.
- **Selection** is Widget-only. `selectCell()` is public (browser-callable), so it ignores unknown keys and non-interactive widgets; consumers override `isInteractive()`, `cellSelected()`, `cellDeselected()`. Keys are canonical cell keys (ISO dates, tile keys).
- **Tile keys**: `TileLayout::resolveKey()` maps data keys to tiles (exact by default); `UsStatesLayout` overrides it for any-case abbreviations and full names. Values resolving to the same tile are summed. Layouts validate duplicate keys and coordinate collisions; rows/columns are 1-based.
- Deliberate API deviations: `getData()` is `protected` (public Livewire methods are callable from the browser), and the entry tooltip override is `cellTooltip()` because `Entry::tooltip()` already exists. There is no Plugin class.
- Future Matrix heatmap should reuse `Support/`, the shared components and the adapters; the presentation settings duplicated between the two resolvers are the thing to extract when a third arrives.

## Tests

Pest with Orchestra Testbench (`WithWorkbench`). Tests are grouped by family (`tests/Support`, `tests/Calendar`, `tests/Tiles`, `tests/UsStates`) with fixtures in `tests/Fixtures`. Tests pin dates (e.g. an end date of 2026-10-20) instead of relying on today. Livewire widget tests toggle interactivity through a static `$interactive` flag on the fixture, reset in `afterEach`.

## Workbench, screenshots and docs

- `workbench/` has a dashboard and an entries page per family. `Workbench\App\Support\SampleData` generates seeded, deterministic data, and every calendar ends on `SampleData::today()` (a fixed date) so screenshots are reproducible. Keep new fixtures deterministic.
- `focus.php` (awcodes/focus) captures light/dark PNGs into `docs/assets/`, and renders share cards (Open Graph, GitHub social, Filament directory thumbnail, plain) into `art/` from awcodes/focus-templates pinned at a tag. Cards use the dedicated `card-*` screenshots, framed to the templates' 1400x816 slots. Widget subjects are selected by Thermal's widget class plus the heatmap's `aria-label` (its heading); entry sections use `data-focus` hooks added in the Workbench with `extraAttributes()`. Dashboard widgets are lazy, so captures scroll the page first.
- `docs/` follows the awcodes documentation hub contract (`docs/docs.yml` navigation, front matter on every page, relative `.md` links, images as `#gh-light-mode-only` / `#gh-dark-mode-only` pairs). Keep `README.md` as the overview and the docs as the reference.
- CI (`.github/workflows/ci.yml`) uses the shared `awcodes/.github` workflow with a Filament 4 and 5 matrix.
