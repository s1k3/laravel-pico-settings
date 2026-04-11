<?php

namespace Pico\Settings\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeSettingsMigrationCommand extends Command
{
    protected $signature = 'pico:settings-table-migration';
    protected $description = 'Generate the pico-settings migration file';

    public function handle(): int
    {
        $migrationsPath = database_path('migrations');
        $filename = date('Y_m_d_His').'_create_settings_table.php';
        $destination = $migrationsPath.DIRECTORY_SEPARATOR.$filename;

        $existing = File::glob($migrationsPath.DIRECTORY_SEPARATOR.'*_create_settings_table.php');

        if (! empty($existing)) {
            $this->warn('A settings migration already exists: '.basename($existing[0]));
            return self::FAILURE;
        }

        $stub = File::get($this->resolveStubPath());

        File::ensureDirectoryExists($migrationsPath);
        File::put($destination, $stub);

        $this->info('Migration created: database/migrations/'.$filename);

        return self::SUCCESS;
    }

    protected function resolveStubPath(): string
    {
        return match (true) {
            File::exists($published) => base_path('stubs/pico-settings/create_settings_table.stub'),
            default => __DIR__.'/../../stubs/create_settings_table.stub',
        };
    }
}
