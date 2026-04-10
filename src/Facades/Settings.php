<?php

namespace Pico\Settings\Facades;

use Illuminate\Support\Facades\Facade;
use Pico\Settings\SettingsBuilder;
use Pico\Settings\SettingsManager;

/**
 * @method static SettingsBuilder for(\Illuminate\Database\Eloquent\Model $user)
 * @method static SettingsBuilder model(\Illuminate\Database\Eloquent\Model|string $model)
 * @method static mixed          get(string|array $key, mixed $default = null)
 * @method static void           set(string|array $key, mixed $value = null)
 * @method static void           delete(string|array|null $key = null)
 *
 * @see SettingsManager
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'pico.settings';
    }
}
