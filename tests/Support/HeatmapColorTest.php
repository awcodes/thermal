<?php

declare(strict_types=1);

use Awcodes\Thermal\Support\HeatmapColor;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

/**
 * @return array<string, string>
 */
function heatmapVariables(string $color): array
{
    $variables = [];

    foreach (explode(';', HeatmapColor::getCssVariables($color, alias: 'test')) as $declaration) {
        [$name, $value] = explode(':', $declaration, 2);
        $variables[$name] = $value;
    }

    return $variables;
}

it('maps every shade of the Filament color', function () {
    $variables = heatmapVariables('success');

    expect($variables['--color-50'])->toBe('var(--success-50)')
        ->and($variables['--color-950'])->toBe('var(--success-950)');
});

it('gives every level readable label text in every Filament palette', function () {
    $backgrounds = ['--thermal-text-' => [1 => 200, 2 => 400, 3 => 600, 4 => 800], '--thermal-dark-text-' => [1 => 900, 2 => 700, 3 => 500, 4 => 300]];

    FilamentColor::register(Color::all());

    foreach (Color::all() as $name => $palette) {
        $variables = heatmapVariables($name);

        foreach ($backgrounds as $prefix => $levels) {
            foreach ($levels as $level => $shade) {
                $text = match ($variables[$prefix . $level]) {
                    '#fff' => 'oklch(1 0 0)',
                    '#000' => 'oklch(0 0 0)',
                    'var(--color-950)' => $palette[950],
                };

                expect(Color::calculateContrastRatio($palette[$shade], $text))
                    ->toBeGreaterThanOrEqual(4.5, "{$name} {$prefix}{$level}");
            }
        }
    }
});

it('falls back to fixed label colors for unregistered colors', function () {
    $variables = heatmapVariables('not-registered');

    expect($variables['--thermal-text-1'])->toBe('var(--color-950)')
        ->and($variables['--thermal-text-4'])->toBe('#fff')
        ->and($variables['--thermal-dark-text-4'])->toBe('var(--color-950)');
});
