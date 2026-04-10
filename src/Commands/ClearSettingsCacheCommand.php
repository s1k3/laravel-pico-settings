<?php

namespace Pico\Settings\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearSettingsCacheCommand extends Command
{
    protected $signature = 'pico:clear';
    protected $description = 'Delete all pico-settings JSON cache files';

    public function handle(): int
    {
        $path = config('pico-settings.cache.path', storage_path('app/pico-settings'));

        if (! File::isDirectory($path)) {
            $this->info('No cache directory found. Nothing to clear.');
            return self::SUCCESS;
        }

        $files = File::glob($path.DIRECTORY_SEPARATOR.'*.json');

        if (empty($files)) {
            $this->info('Cache is already empty.');
            return self::SUCCESS;
        }

        File::delete($files);

        $this->info('Deleted '.count($files).' cache file(s).');

        return self::SUCCESS;
    }
}
