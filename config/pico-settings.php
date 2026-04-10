<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Users Table
    |--------------------------------------------------------------------------
    | The table name used for the users relation on the settings table.
    */
    'user_table' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    | Enable JSON file caching. When enabled, settings are cached to a JSON
    | file per user (or a global file for non-user-specific settings).
    | The cache is invalidated on every set() operation.
    */
    'cache' => [
        'enabled' => true,
        'path'    => storage_path('app/pico-settings'),
    ],

];
