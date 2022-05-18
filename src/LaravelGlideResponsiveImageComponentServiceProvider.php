<?php

namespace Johncarter\LaravelGlideResponsiveImageComponent;

use Johncarter\LaravelGlideResponsiveImageComponent\Commands\GenerateImagePresets;
use Johncarter\LaravelGlideResponsiveImageComponent\Components\ResponsiveImage;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelGlideResponsiveImageComponentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-glide-responsive-image-component')
            ->hasConfigFile('laravel-glide-responsive-image-component')
            ->hasViews('laravel-glide-responsive-image-component')
            ->hasViewComponent('glide', ResponsiveImage::class)
            ->hasCommand(GenerateImagePresets::class);
    }
}
