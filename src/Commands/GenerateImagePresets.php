<?php

namespace Johncarter\LaravelGlideResponsiveImageComponent\Commands;

use Illuminate\Console\Command;
use Johncarter\LaravelGlideResponsiveImageComponent\LaravelGlideResponsiveImageComponent;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class GenerateImagePresets extends Command
{
    public $signature = 'glide-responsive-images:generate {--force}';

    public $description = 'Generates conversions for images to avoid doing it on first request.';

    public function handle(): int
    {
        // Get all the images in a given source
        $files = Finder::create()
            ->in(public_path('assets'))
            ->files()
            ->name(['*.jpg', '*.jpeg', '*.png'])
            ->notPath('conversions')
            ->notPath('livewire-tmp');

        $bar = $this->output->createProgressBar($files->count());

        $bar->setFormat('very_verbose');

        $bar->start();

        // Pre-generate the image conversions
        collect(iterator_to_array($files))->each(function (SplFileInfo $file) use (&$bar) {
            LaravelGlideResponsiveImageComponent::make($file->getPathname());

            $bar->advance();
        });

        $bar->finish();

        $this->info(' Done. ' . "\n");

        return self::SUCCESS;
    }
}
