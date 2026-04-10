<?php

use Pico\Settings\SettingsManager;

if (! function_exists('settings')) {
    function settings(): SettingsManager
    {
        return app('pico.settings');
    }
}
