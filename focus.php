<?php

declare(strict_types=1);

use Awcodes\Focus\Card;
use Awcodes\Focus\Enums\Size;
use Awcodes\Focus\Enums\Theme;
use Awcodes\Focus\Enums\Viewport;
use Awcodes\Focus\Screenshot;
use Awcodes\Focus\ScreenshotSuite;
use Playwright\Page\PageInterface;

/*
 * Documentation screenshots for Thermal, generated with awcodes/focus from the Workbench (run `composer build`
 * first). The Workbench data is seeded and every calendar ends on SampleData::today(), so each build renders the
 * same cells.
 *
 * Widgets have no extra-attributes hook, so widget subjects are found by Thermal's own widget class and the
 * heatmap's accessible name, which is the widget heading. Entry sections carry data-focus hooks.
 */

// Dashboard widgets are lazy and load as they scroll into view. Scrolling through the page starts every request,
// and Focus then waits for them to finish before framing the subject.
$loadWidgets = function (PageInterface $page): void {
    $page->evaluate(<<<'JS'
        async () => {
            const pause = () => new Promise((resolve) => setTimeout(resolve, 150));

            for (let y = 0; y <= document.body.scrollHeight; y += 400) {
                window.scrollTo(0, y);
                await pause();
            }

            window.scrollTo(0, 0);
        }
        JS);
};

// The awcodes card templates frame each screenshot at 1400x816, showing slot 1 dark and slot 2 light.
$card = [1400, 816];

$calendarWidget = fn (string $heading): string => ".fi-thermal-calendar-heatmap-widget:has([aria-label=\"{$heading}\"])";
$tileWidget = fn (string $heading): string => ".fi-thermal-tile-heatmap-widget:has([aria-label=\"{$heading}\"])";

return ScreenshotSuite::make()
    ->padding(16)
    ->screenshots([
        Screenshot::make('calendar-widget')
            ->visit('/admin/calendar')
            ->ready($loadWidgets)
            ->focus($calendarWidget('Orders')),

        Screenshot::make('calendar-entries')
            ->visit('/admin/calendar-entries')
            ->focus('[data-focus="calendar-entries"]'),

        Screenshot::make('zero-and-missing')
            ->visit('/admin/calendar')
            ->ready($loadWidgets)
            ->focus($calendarWidget('Deployments')),

        Screenshot::make('automatic-scaling')
            ->visit('/admin/calendar')
            ->ready($loadWidgets)
            ->focus($calendarWidget('Downloads')),

        Screenshot::make('tile-widget')
            ->visit('/admin/tiles')
            ->ready($loadWidgets)
            ->focus($tileWidget('Requests per rack')),

        Screenshot::make('custom-thresholds')
            ->visit('/admin/tiles')
            ->ready($loadWidgets)
            ->focus($tileWidget('Power draw')),

        Screenshot::make('us-states-widget')
            ->visit('/admin/states')
            ->ready($loadWidgets)
            ->focus($tileWidget('Lead activity across the U.S.')),

        Screenshot::make('us-states-entries')
            ->visit('/admin/tile-entries')
            ->focus('[data-focus="us-states-entries"]'),

        // Only the store openings widget is interactive, so it is the only one with buttons.
        Screenshot::make('cell-selection')
            ->visit('/admin/states')
            ->ready($loadWidgets)
            ->click('button[data-key="TX"]')
            ->waitFor('button[data-key="TX"][aria-pressed="true"]')
            ->focus($tileWidget('Store openings')),

        // At phone width the tiles reach their minimum size and the map scrolls inside the widget.
        Screenshot::make('us-states-mobile')
            ->viewportSize(Viewport::Mobile)
            ->visit('/admin/states')
            ->ready($loadWidgets)
            ->focus($tileWidget('Lead activity across the U.S.')),

        // Card slot 1: the U.S. States widget is taller than the slot, so the frame grows around it to the slot's
        // shape instead of cutting off its legend as a viewport capture would. Growing it takes in the top of the
        // next row of widgets, which are hidden so the frame ends in empty space.
        Screenshot::make('card-states')
            ->viewportSize(...$card)
            ->themes([Theme::Dark])
            ->visit('/admin/states')
            ->ready($loadWidgets)
            ->hide($tileWidget('Store openings'), $tileWidget('Support tickets'))
            ->focus($tileWidget('Lead activity across the U.S.'))
            ->minSize(...$card),

        // Card slot 2: the calendar dashboard with the panel around it. The template shows it light, but cards
        // render dark, and a card's screenshots must exist in its theme, so both are captured.
        Screenshot::make('card-calendar')
            ->viewportSize(...$card)
            ->visit('/admin/calendar')
            ->ready($loadWidgets)
            ->viewport(),
    ])
    ->cardTemplates('https://github.com/awcodes/focus-templates/tree/v2.1.0/dist')
    ->cards([
        // Open Graph and the GitHub social preview share one 2400x1260 template; GitHub crops 30px top and bottom.
        Card::make('social')
            ->template('two-up-wide')
            ->title('Thermal')
            ->screenshots(['card-states', 'card-calendar'])
            ->sizes([Size::OpenGraph, Size::GitHubSocial]),

        // The Filament plugin directory's 2560x1440 thumbnail.
        Card::make('thumbnail')
            ->template('two-up')
            ->title('Thermal')
            ->screenshots(['card-states', 'card-calendar'])
            ->sizes([Size::Filament]),

        // Unbranded 16:9 image for aw.codes, which adds its own heading: the same screenshots, no text or logo.
        Card::make('plain')
            ->template('two-up-plain')
            ->screenshots(['card-states', 'card-calendar'])
            ->sizes([[2560, 1440]])
            ->scale(1),
    ]);
