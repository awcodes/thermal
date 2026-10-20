<?php

declare(strict_types=1);

namespace Awcodes\Thermal;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ThermalServiceProvider extends PackageServiceProvider
{
    public static string $name = 'thermal';

    public static string $viewNamespace = 'thermal';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews(static::$viewNamespace)
            ->hasTranslations()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->askToStarRepoOnGitHub('awcodes/thermal');
            });
    }
}
