<?php

use Pico\Settings\Facades\Settings;
use Pico\Settings\Tests\User;

// ── get / set ─────────────────────────────────────────────────────────────────

it('sets and gets a global value', function () {
    Settings::set('theme', 'dark');

    expect(Settings::get('theme'))->toBe('dark');
});

it('returns the default when a key is missing', function () {
    expect(Settings::get('theme', 'light'))->toBe('light');
});

it('sets and gets a value for a user', function () {
    $user = User::create(['name' => 'Alice']);

    Settings::for($user)->set('locale', 'en');

    expect(Settings::for($user)->get('locale'))->toBe('en');
});

it('isolates settings between users', function () {
    $alice = User::create(['name' => 'Alice']);
    $bob   = User::create(['name' => 'Bob']);

    Settings::for($alice)->set('locale', 'en');
    Settings::for($bob)->set('locale', 'fr');

    expect(Settings::for($alice)->get('locale'))->toBe('en')
        ->and(Settings::for($bob)->get('locale'))->toBe('fr');
});

it('sets and gets multiple values at once', function () {
    $user = User::create(['name' => 'Alice']);

    Settings::for($user)->set(['locale' => 'en', 'timezone' => 'UTC']);

    expect(Settings::for($user)->get(['locale', 'timezone']))
        ->toBe(['locale' => 'en', 'timezone' => 'UTC']);
});

it('fills missing keys with the default when getting multiple', function () {
    expect(Settings::get(['a', 'b'], 'fallback'))
        ->toBe(['a' => 'fallback', 'b' => 'fallback']);
});

it('sets and gets a value scoped to a model', function () {
    Settings::model(User::class)->set('max_items', '50');

    expect(Settings::model(User::class)->get('max_items'))->toBe('50');
});

it('keeps user+model scope isolated from other scopes', function () {
    $user = User::create(['name' => 'Alice']);

    Settings::for($user)->model(User::class)->set('view', 'grid');

    expect(Settings::for($user)->model(User::class)->get('view'))->toBe('grid')
        ->and(Settings::for($user)->get('view'))->toBeNull()
        ->and(Settings::model(User::class)->get('view'))->toBeNull();
});

it('overwrites an existing value', function () {
    Settings::set('theme', 'dark');
    Settings::set('theme', 'light');

    expect(Settings::get('theme'))->toBe('light');
});

it('works via the settings() helper', function () {
    settings()->set('key', 'value');

    expect(settings()->get('key'))->toBe('value');
});

// ── delete ────────────────────────────────────────────────────────────────────

it('deletes a single key', function () {
    Settings::set(['theme' => 'dark', 'locale' => 'en']);

    Settings::delete('theme');

    expect(Settings::get('theme'))->toBeNull()
        ->and(Settings::get('locale'))->toBe('en');
});

it('deletes multiple keys at once', function () {
    Settings::set(['a' => '1', 'b' => '2', 'c' => '3']);

    Settings::delete(['a', 'b']);

    expect(Settings::get('a'))->toBeNull()
        ->and(Settings::get('b'))->toBeNull()
        ->and(Settings::get('c'))->toBe('3');
});

it('deletes all keys in a scope when no key is given', function () {
    Settings::set(['a' => '1', 'b' => '2']);

    Settings::delete();

    expect(Settings::get('a'))->toBeNull()
        ->and(Settings::get('b'))->toBeNull();
});

it('deletes a key within a user scope', function () {
    $user = User::create(['name' => 'Alice']);

    Settings::for($user)->set(['locale' => 'en', 'timezone' => 'UTC']);
    Settings::for($user)->delete('locale');

    expect(Settings::for($user)->get('locale'))->toBeNull()
        ->and(Settings::for($user)->get('timezone'))->toBe('UTC');
});

it('does not affect other scopes when deleting', function () {
    $user = User::create(['name' => 'Alice']);

    Settings::set('theme', 'dark');
    Settings::for($user)->set('theme', 'light');

    Settings::for($user)->delete('theme');

    expect(Settings::for($user)->get('theme'))->toBeNull()
        ->and(Settings::get('theme'))->toBe('dark');
});

it('deletes within a combined user+model scope', function () {
    $user = User::create(['name' => 'Alice']);

    Settings::for($user)->model(User::class)->set('view', 'grid');
    Settings::for($user)->model(User::class)->delete('view');

    expect(Settings::for($user)->model(User::class)->get('view'))->toBeNull();
});
