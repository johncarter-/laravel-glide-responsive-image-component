<?php

namespace Johncarter\LaravelGlideResponsiveImageComponent\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Johncarter\LaravelGlideResponsiveImageComponent\LaravelGlideResponsiveImageComponent
 */
class LaravelGlideResponsiveImageComponent extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-glide-responsive-image-component';
    }
}
