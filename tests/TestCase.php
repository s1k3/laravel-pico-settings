<?php

namespace Pico\Settings\Tests;

use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Pico\Settings\SettingsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [SettingsServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('pico-settings.cache.enabled', false);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Create a minimal users table for foreign key tests.
        $this->app['db']->connection()->getSchemaBuilder()->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }
}

/**
 * Minimal User stub for tests.
 */
class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name'];
    public $timestamps = true;
}
