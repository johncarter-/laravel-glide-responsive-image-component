<?php

namespace Johncarter\LaravelGlideResponsiveImageComponent;

use Illuminate\Support\Facades\File;

class LaravelGlideResponsiveImageComponent
{
    public ?string $imagePath = null;
    public ?int $width = null;
    public ?string $filename = '';
    public ?string $conversionSrcsetString = '';
    public ?string $conversionDefaultUrl = '';
    public ?string $conversionExtension = 'webp';

    public static function make(string $path, ?int $width = null)
    {
        return new LaravelGlideResponsiveImageComponent($path, $width);
    }

    public function __construct(string $path, ?int $width = null)
    {
        if (!file_exists($path) || is_dir($path)) return;

        $this->imagePath = $path;

        $this->width = $width;

        $this->filename = pathinfo($this->imagePath)['filename'];

        $this->generateConversions();
    }

    public function generateConversions()
    {
        $breakpoints = collect(config('laravel-glide-responsive-image-component.breakpoints'));

        $maxRequestedWidth = $this->width ? $this->width : getimagesize($this->imagePath)[0] ?? 1600;

        $skipRest = false;

        $breakpoints->each(function ($breakpointWidth) use ($maxRequestedWidth, &$skipRest) {
            if ($skipRest) return;

            if ($breakpointWidth > $maxRequestedWidth) $skipRest = true;

            $this->conversionSrcsetString .= $this->firstOrCreateConversion($this->imagePath, $breakpointWidth) . ' ' . $breakpointWidth . 'w,';

            // Keep overwriting the default with the next largest conversion
            $this->conversionDefaultUrl = $this->firstOrCreateConversion($this->imagePath, $breakpointWidth);
        });
    }

    public function firstOrCreateConversion($imagePath, int $conversionWidth = 1600): string
    {
        $conversionsDirectoryPath = config('laravel-glide-responsive-image-component.conversion_directory') . '/' . $this->filename . '/' . $conversionWidth; // e.g. public/assets/conversions/asdhasd-filename-asdasd15/50
        $conversionFilePath = $conversionsDirectoryPath . '/' . $this->filename . '.' . $this->conversionExtension; // e.g. public/assets/conversions/asdhasd-filename-asdasd15/50/filename.jpg

        File::ensureDirectoryExists($conversionsDirectoryPath);

        if (!File::exists($conversionFilePath)) {
            \Spatie\Glide\GlideImageFacade::create($imagePath)
                ->modify(['w' => $conversionWidth])
                ->save($conversionFilePath);
        }

        return config('laravel-glide-responsive-image-component.conversion_url') . '/' . $this->filename . '/' . $conversionWidth . '/' . $this->filename . '.' . $this->conversionExtension;
    }
}
