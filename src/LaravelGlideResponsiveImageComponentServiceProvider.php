<?php

namespace Johncarter-\LaravelGlideResponsiveImageComponent;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Johncarter-\LaravelGlideResponsiveImageComponent\Commands\LaravelGlideResponsiveImageComponentCommand;

class LaravelGlideResponsiveImageComponentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-glide-responsive-image-component')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-glide-responsive-image-component_table')
            ->hasCommand(LaravelGlideResponsiveImageComponentCommand::class);
    }
}
