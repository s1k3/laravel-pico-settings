<?php

namespace Pico\Settings\Tests;

use Pico\Settings\Facades\Settings;

class SettingsTest extends TestCase
{
    public function test_set_and_get_global_value(): void
    {
        Settings::set('theme', 'dark');

        $this->assertSame('dark', Settings::get('theme'));
    }

    public function test_get_returns_default_when_missing(): void
    {
        $this->assertSame('light', Settings::get('theme', 'light'));
    }

    public function test_set_and_get_for_user(): void
    {
        $user = User::create(['name' => 'Alice']);

        Settings::for($user)->set('locale', 'en');

        $this->assertSame('en', Settings::for($user)->get('locale'));
    }

    public function test_user_settings_are_isolated(): void
    {
        $alice = User::create(['name' => 'Alice']);
        $bob   = User::create(['name' => 'Bob']);

        Settings::for($alice)->set('locale', 'en');
        Settings::for($bob)->set('locale', 'fr');

        $this->assertSame('en', Settings::for($alice)->get('locale'));
        $this->assertSame('fr', Settings::for($bob)->get('locale'));
    }

    public function test_set_and_get_multiple_values(): void
    {
        $user = User::create(['name' => 'Alice']);

        Settings::for($user)->set(['locale' => 'en', 'timezone' => 'UTC']);

        $result = Settings::for($user)->get(['locale', 'timezone']);

        $this->assertSame(['locale' => 'en', 'timezone' => 'UTC'], $result);
    }

    public function test_get_multiple_missing_keys_uses_default(): void
    {
        $result = Settings::get(['a', 'b'], 'fallback');

        $this->assertSame(['a' => 'fallback', 'b' => 'fallback'], $result);
    }

    public function test_set_and_get_with_model_scope(): void
    {
        Settings::model(User::class)->set('max_items', '50');

        $this->assertSame('50', Settings::model(User::class)->get('max_items'));
    }

    public function test_model_and_user_scope_combined(): void
    {
        $user = User::create(['name' => 'Alice']);

        Settings::for($user)->model(User::class)->set('view', 'grid');

        $this->assertSame('grid', Settings::for($user)->model(User::class)->get('view'));
        // Different scope should not bleed through.
        $this->assertNull(Settings::for($user)->get('view'));
        $this->assertNull(Settings::model(User::class)->get('view'));
    }

    public function test_set_overwrites_existing_value(): void
    {
        Settings::set('theme', 'dark');
        Settings::set('theme', 'light');

        $this->assertSame('light', Settings::get('theme'));
    }

    public function test_helper_function_works(): void
    {
        settings()->set('key', 'value');

        $this->assertSame('value', settings()->get('key'));
    }
}
