<?php

namespace Johncarter\LaravelGlideResponsiveImageComponent\View\Components;

use Illuminate\Support\Facades\File;
use Illuminate\View\Component;

class ResponsiveImage extends Component
{
    public string $src;
    public string $conversionExtension;
    public string $filename;
    public ?string $conversionDefaultUrl = null;
    public ?string $conversionSrcsetString = '';

    public function __construct(string $src, ?string $conversionExtension = 'webp')
    {
        $this->conversionExtension = $conversionExtension;

        $this->src = $src;

        $this->filename = pathinfo($this->src)['filename'];
    }

    public function render()
    {
        if (!file_exists($this->src) || is_dir($this->src)) {
            $this->conversionDefaultUrl = $this->src;

            return view('components.responsive-image');
        }

        $breakpoints = collect([
            'none' => 0,
            'xs' => 480,
            'sm' => 640,
            'md' => 768,
            'lg' => 1024,
            'xl' => 1280,
            '2xl' => 1536,
        ]);

        $srcWidth = getimagesize($this->src)[0];

        $breakpoints->each(function ($width) use ($srcWidth) {
            if ($width > $srcWidth) return;

            $this->conversionSrcsetString .= $this->firstOrCreateConversion($width) . ' ' . $width . 'w,';

            // Keep overwriting the default with the next largest conversion
            $this->conversionDefaultUrl = $this->firstOrCreateConversion($width);
        });

        return view('components.responsive-image');
    }

    protected function firstOrCreateConversion(int $conversionWidth = 1600): string
    {
        $conversionsDirectoryPath = public_path('assets/conversions') . '/' . $this->filename . '/' . $conversionWidth; // e.g. public/assets/conversions/asdhasd-filename-asdasd15/50
        $conversionFilePath = $conversionsDirectoryPath . '/' . $this->filename . '.' . $this->conversionExtension; // e.g. public/assets/conversions/asdhasd-filename-asdasd15/50/filename.jpg

        File::ensureDirectoryExists($conversionsDirectoryPath);

        if (!File::exists($conversionFilePath)) {
            ray('generating ' . $conversionFilePath);
            $convertedImagePath = \Spatie\Glide\GlideImageFacade::create($this->src)
                ->modify(['w' => $conversionWidth])
                ->save($conversionFilePath);
        }

        return '/assets/conversions/' . $this->filename . '/' . $conversionWidth . '/' . $this->filename . '.' . $this->conversionExtension;
    }
}
