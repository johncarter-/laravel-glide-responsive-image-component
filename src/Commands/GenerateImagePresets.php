<?php

namespace Johncarter\LaravelGlideResponsiveImageComponent\Commands;

use Illuminate\Console\Command;

class GenerateImagePresets extends Command
{
    public $signature = 'glide-responsive-images:generate {--force}';

    public $description = 'Generates conversions for images to avoid doing it on first request.';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
