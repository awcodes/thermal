---
title: Installation
description: Install Thermal and register its views with your Filament theme.
---

# Installation

## Compatibility

| Filament version | Package version |
|------------------|-----------------|
| 4.x & 5.x        | 1.x             |

Thermal requires PHP 8.2 or later and `filament/filament`.

## Install the package

Install the package via Composer:

```bash
composer require awcodes/thermal
```

The service provider is registered automatically through Laravel's package discovery, so
there is nothing to add to `config/app.php`.

## Registering the views with your theme

Thermal's markup is styled with Tailwind classes, which means your theme has to be
told to scan the package's Blade files when it builds its stylesheet. Without this step
the components render, but unstyled.

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels, follow the
> instructions in the [Filament documentation](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme)
> before continuing.

Once you have a custom theme, add the package's views as a source in your theme's CSS
file — or in your application's CSS file if you are using the standalone Filament
packages:

```css
@source '../../../../vendor/awcodes/thermal/resources/**/*.blade.php';
```

Then rebuild your assets so the new classes are compiled in.

## Publishing the translations

Level names such as "high activity", "no data", the legend labels and the default calendar
tooltip are translation strings. To change their wording or add a language, publish them:

```bash
php artisan vendor:publish --tag="thermal-translations"
```

## Publishing the views

Publishing the views is optional, and only needed if you want to change the components'
markup rather than their configurable options:

```bash
php artisan vendor:publish --tag="thermal-views"
```

Once published, the views live in your application and take precedence over the
package's own. Keep in mind that published views no longer receive upstream changes when
the package is updated.

