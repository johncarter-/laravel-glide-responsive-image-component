<?php

namespace Johncarter\LaravelGlideResponsiveImageComponent\Commands;

use Illuminate\Console\Command;

class LaravelGlideResponsiveImageComponentCommand extends Command
{
    public $signature = 'laravel-glide-responsive-image-component';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
