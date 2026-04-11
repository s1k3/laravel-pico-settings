<?php

namespace Pico\Settings;

use Illuminate\Support\ServiceProvider;
use Pico\Settings\Commands\ClearSettingsCacheCommand;
use Pico\Settings\Commands\MakeSettingsMigrationCommand;
use Pico\Settings\Contracts\SettingsRepositoryInterface;
use Pico\Settings\Repositories\CachedSettingsRepository;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pico-settings.php', 'pico-settings');

        $this->app->bind(SettingsRepositoryInterface::class, CachedSettingsRepository::class);

        $this->app->bind('pico.settings', fn ($app) => new SettingsManager(
            $app->make(SettingsRepositoryInterface::class)
        ));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ClearSettingsCacheCommand::class,
                MakeSettingsMigrationCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/pico-settings.php' => config_path('pico-settings.php'),
            ], 'pico-settings-config');

            $this->publishes([
                __DIR__.'/../stubs/create_settings_table.stub' => base_path('stubs/pico-settings/create_settings_table.stub'),
            ], 'pico-settings-stubs');
        }
    }
}
