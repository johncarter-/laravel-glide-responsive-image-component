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
    public $loading;
    public ?string $placeholderImage = null;

    public function __construct(string $path, ?int $width = null)
    {
        try {
            $imageData = LaravelGlideResponsiveImageComponent::make($path, $width);

            $this->conversionSrcsetString = $imageData->conversionSrcsetString ?? '';
            $this->conversionDefaultUrl = $imageData->conversionDefaultUrl ?? '';
        } catch (\Exception $e) {
            // Log error but don't break the page
            \Log::warning('Failed to generate responsive image', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            
            // Set empty values so placeholder shows
            $this->conversionSrcsetString = '';
            $this->conversionDefaultUrl = '';
        }

        $this->placeholderImage = config('laravel-glide-responsive-image-component.placeholder_image');
    }

    public function render(): View
    {
        return view('laravel-glide-responsive-image-component::components.responsive-image');
    }
}
