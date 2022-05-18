<?php

namespace Johncarter\LaravelGlideResponsiveImageComponent\Components;

use Illuminate\Support\Facades\File;
use Illuminate\View\Component;

class ResponsiveImage extends Component
{
    public ?string $imagePath = null;
    public ?string $filename = '';
    public ?string $conversionSrcsetString = '';
    public ?string $conversionDefaultUrl = '';
    public ?string $conversionExtension = 'webp';

    public function __construct($src)
    {
        if (!file_exists($src) || is_dir($src)) {
            $this->conversionDefaultUrl = $src;

            return view('laravel-glide-responsive-image-component::components.responsive-image');
        }

        $this->imagePath = $src;

        $this->filename = pathinfo($this->imagePath)['filename'];

        $this->getResponsiveData($src);
    }

    public function render()
    {
        return view('laravel-glide-responsive-image-component::components.responsive-image');
    }

    public function getResponsiveData(string $imagePath): void
    {
        $breakpoints = collect(config('laravel-glide-responsive-image-component.breakpoints'));

        $srcWidth = getimagesize($imagePath)[0];

        $breakpoints->each(function ($breakpointWidth) use ($imagePath, $srcWidth, &$data) {
            if ($breakpointWidth > $srcWidth) return;

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
