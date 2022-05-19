<?php

namespace Johncarter\LaravelGlideResponsiveImageComponent\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\View\Component;

class ResponsiveImage extends Component
{
    public ?string $imagePath = null;
    public ?string $filename = '';
    public ?string $conversionSrcsetString = '';
    public ?string $conversionDefaultUrl = '';
    public ?string $conversionExtension = 'webp';
    public ?int $width = null;

    public function __construct(string $src, ?int $width = null)
    {
        $this->imagePath = $src;

        $this->width = $width;
    }

    public function render(): View
    {
        if (!file_exists($this->imagePath) || is_dir($this->imagePath)) {
            $this->conversionDefaultUrl = $this->imagePath;

            return view('laravel-glide-responsive-image-component::components.responsive-image');
        }

        $this->filename = pathinfo($this->imagePath)['filename'];

        $this->getResponsiveData($this->imagePath);

        return view('laravel-glide-responsive-image-component::components.responsive-image');
    }

    public function getResponsiveData(string $imagePath): void
    {
        $breakpoints = collect(config('laravel-glide-responsive-image-component.breakpoints'));

        $maxRequestedWidth = $this->width ? $this->width : getimagesize($imagePath)[0];

        $skipRest = false;

        $breakpoints->each(function ($breakpointWidth) use ($imagePath, $maxRequestedWidth, &$skipRest) {
            if ($skipRest) return;

            if ($breakpointWidth > $maxRequestedWidth) $skipRest = true;

            $this->conversionSrcsetString .= $this->firstOrCreateConversion($imagePath, $breakpointWidth) . ' ' . $breakpointWidth . 'w,';

            // Keep overwriting the default with the next largest conversion
            $this->conversionDefaultUrl = $this->firstOrCreateConversion($imagePath, $breakpointWidth);
        });;
    }

    protected function firstOrCreateConversion($imagePath, int $conversionWidth = 1600): string
    {
        $conversionsDirectoryPath = config('laravel-glide-responsive-image-component.conversion_directory') . '/' . $this->filename . '/' . $conversionWidth; // e.g. public/assets/conversions/asdhasd-filename-asdasd15/50
        $conversionFilePath = $conversionsDirectoryPath . '/' . $this->filename . '.' . $this->conversionExtension; // e.g. public/assets/conversions/asdhasd-filename-asdasd15/50/filename.jpg

        File::ensureDirectoryExists($conversionsDirectoryPath);

        if (!File::exists($conversionFilePath)) {
            \Spatie\Glide\GlideImageFacade::create($imagePath)
                ->modify(['w' => $conversionWidth])
                ->save($conversionFilePath);
        }

        return '/assets/conversions/' . $this->filename . '/' . $conversionWidth . '/' . $this->filename . '.' . $this->conversionExtension;
    }
}
