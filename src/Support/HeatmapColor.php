<?php

declare(strict_types=1);

namespace Awcodes\Thermal\Support;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

use function Filament\Support\get_color_css_variables;

/**
 * The CSS variables a heatmap root needs to color its cells from a Filament color.
 */
final class HeatmapColor
{
    /**
     * WCAG AA for normal-size text, which tile labels are.
     */
    private const MIN_TEXT_CONTRAST = 4.5;

    private const SHADES = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];

    /**
     * The background shade of each populated level, in light and dark mode. These mirror
     * the background classes in the cell component.
     */
    private const BACKGROUNDS = [
        'light' => [1 => 200, 2 => 400, 3 => 600, 4 => 800],
        'dark' => [1 => 900, 2 => 700, 3 => 500, 4 => 300],
    ];

    /**
     * Shade variables for the color, plus `--thermal-text-{level}` and `--thermal-dark-text-{level}`:
     * whichever of white or the color's darkest shade contrasts more with each level's background.
     */
    public static function getCssVariables(string $color, string $alias): string
    {
        $variables = [(string) get_color_css_variables($color, shades: self::SHADES, alias: $alias)];
        $palette = FilamentColor::getColor($color);

        foreach (self::BACKGROUNDS as $theme => $levels) {
            $prefix = $theme === 'dark' ? '--thermal-dark-text-' : '--thermal-text-';

            foreach ($levels as $level => $shade) {
                $variables[] = $prefix . $level . ':' . self::getTextColor($palette, $shade);
            }
        }

        return implode(';', $variables);
    }

    /**
     * @param  array<int | string, string>|null  $palette
     */
    private static function getTextColor(?array $palette, int $background): string
    {
        if (! isset($palette[$background], $palette[950])) {
            return $background >= 600 ? '#fff' : 'var(--color-950)';
        }

        $onWhite = Color::calculateContrastRatio($palette[$background], 'oklch(1 0 0)');
        $onDark = Color::calculateContrastRatio($palette[$background], $palette[950]);

        if (max($onWhite, $onDark) >= self::MIN_TEXT_CONTRAST) {
            return $onWhite >= $onDark ? '#fff' : 'var(--color-950)';
        }

        // Mid-tone shades can miss the ratio with both, but white or black always meets it.
        return $onWhite >= Color::calculateContrastRatio($palette[$background], 'oklch(0 0 0)') ? '#fff' : '#000';
    }
}
