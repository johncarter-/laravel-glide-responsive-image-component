<?php

namespace Johncarter\LaravelGlideResponsiveImageComponent;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LaravelGlideResponsiveImageComponent
{
    public ?string $imagePath = null;
    public ?int $width = null;
    public ?string $filename = '';
    public ?string $conversionSrcsetString = '';
    public ?string $conversionDefaultUrl = '';
    public ?string $conversionExtension = 'webp';
    public bool $isS3Path = false;
    public ?string $s3Path = null;

    public static function make(string $path, ?int $width = null)
    {
        return new LaravelGlideResponsiveImageComponent($path, $width);
    }

    public function __construct(string $path, ?int $width = null)
    {
        $this->width = $width;
        
        // Determine if this is an S3 path or local path
        $this->isS3Path = $this->isS3Path($path);
        
        if ($this->isS3Path) {
            $this->handleS3Path($path);
            
            // If S3 handling didn't set imagePath, fall back to local
            if (!$this->imagePath) {
                $this->handleLocalPath($path);
            }
        } else {
            $this->handleLocalPath($path);
        }

        if (!$this->imagePath) {
            return;
        }

        $this->filename = pathinfo($this->imagePath)['filename'];

        $this->generateConversions();
    }

    protected function isS3Path(string $path): bool
    {
        // Check if S3 is enabled
        if (!config('laravel-glide-responsive-image-component.use_s3', false)) {
            return false;
        }

        // If path starts with / or contains absolute path, it's local
        if (str_starts_with($path, '/') || str_starts_with($path, public_path())) {
            return false;
        }

        // Get configurable assets prefix
        $assetsPrefix = config('laravel-glide-responsive-image-component.s3_assets_prefix', 'assets');
        
        // If path starts with assets prefix, check S3
        if (str_starts_with($path, $assetsPrefix . '/')) {
            // Remove assets prefix for S3 check
            $s3Path = str_replace($assetsPrefix . '/', '', $path);
            $s3Disk = Storage::disk(config('laravel-glide-responsive-image-component.s3_disk', 's3_assets'));
            
            try {
                // Use a faster check - just see if we can access the disk
                // Don't do expensive exists() check for every path
                return true; // Assume S3 path if format matches
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    protected function handleS3Path(string $path): void
    {
        // Get configurable assets prefix
        $assetsPrefix = config('laravel-glide-responsive-image-component.s3_assets_prefix', 'assets');
        
        // Remove assets prefix for S3 operations
        $s3Path = str_replace($assetsPrefix . '/', '', $path);
        $this->s3Path = $s3Path;
        
        $s3Disk = Storage::disk(config('laravel-glide-responsive-image-component.s3_disk', 's3_assets'));

        // Check if file exists in S3
        $existsInS3 = false;
        try {
            $existsInS3 = $s3Disk->exists($s3Path);
        } catch (\Exception $e) {
            // If S3 check fails, fall back to local
            $existsInS3 = false;
        }

        // Determine local path (keep assets prefix for local storage)
        $localPath = public_path($assetsPrefix . '/' . $s3Path);

        // If file exists locally, use it
        if (file_exists($localPath)) {
            $this->imagePath = $localPath;
            return;
        }

        // If file exists in S3 but not locally, download it
        if ($existsInS3 && config('laravel-glide-responsive-image-component.s3_download_on_demand', true)) {
            try {
                $contents = $s3Disk->get($s3Path);
                File::ensureDirectoryExists(dirname($localPath));
                File::put($localPath, $contents);
                
                if (file_exists($localPath)) {
                    $this->imagePath = $localPath;
                }
            } catch (\Exception $e) {
                // If download fails, try local path as fallback
                if (file_exists($localPath)) {
                    $this->imagePath = $localPath;
                }
            }
        }
    }

    protected function handleLocalPath(string $path): void
    {
        // Handle relative paths (e.g., 'assets/products/image.jpg')
        if (!str_starts_with($path, '/') && !str_starts_with($path, public_path())) {
            $path = public_path($path);
        }

        if (!file_exists($path) || is_dir($path)) {
            return;
        }

        $this->imagePath = $path;
    }

    public function generateConversions()
    {
        if (!$this->imagePath) {
            return;
        }

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
