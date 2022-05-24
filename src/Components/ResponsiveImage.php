<?php

namespace Johncarter\LaravelGlideResponsiveImageComponent\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\View\Component;
use Johncarter\LaravelGlideResponsiveImageComponent\LaravelGlideResponsiveImageComponent;

class ResponsiveImage extends Component
{
    public ?string $conversionSrcsetString = '';
    public ?string $conversionDefaultUrl = '';

    public function __construct(string $path, ?int $width = null)
    {
        $imageData = LaravelGlideResponsiveImageComponent::make($path, $width);

        $this->conversionSrcsetString = $imageData->conversionSrcsetString;

        $this->conversionDefaultUrl  = $imageData->conversionDefaultUrl;
    }

    public function render(): View
    {
        return view('laravel-glide-responsive-image-component::components.responsive-image');
    }
}
