<?php

use Pico\Settings\Cache\JsonSettingsCache;

beforeEach(function () {
    $this->cachePath = storage_path('app/pico-settings-test-'.uniqid());
    config(['pico-settings.cache.path' => $this->cachePath]);
    config(['pico-settings.cache.enabled' => true]);

    $this->cache = new JsonSettingsCache;
});

afterEach(function () {
    if (is_dir($this->cachePath)) {
        array_map('unlink', glob($this->cachePath.DIRECTORY_SEPARATOR.'*.json'));
        rmdir($this->cachePath);
    }
});

// ── global scope (userId = null, model = null) ────────────────────────────────

it('puts and gets global settings (no user, no model)', function () {
    $this->cache->put(null, null, ['logo' => 'image/logo.png', 'theme' => 'dark']);

    expect($this->cache->get(null, null))
        ->toBe(['logo' => 'image/logo.png', 'theme' => 'dark']);
});

it('returns null when the global cache file does not exist', function () {
    expect($this->cache->get(null, null))->toBeNull();
});

it('overwrites existing global cache on put', function () {
    $this->cache->put(null, null, ['theme' => 'dark']);
    $this->cache->put(null, null, ['theme' => 'light']);

    expect($this->cache->get(null, null))->toBe(['theme' => 'light']);
});

it('forgets the global cache file', function () {
    $this->cache->put(null, null, ['theme' => 'dark']);
    $this->cache->forget(null, null);

    expect($this->cache->get(null, null))->toBeNull();
});

it('forget does nothing when global cache file does not exist', function () {
    expect(fn () => $this->cache->forget(null, null))->not->toThrow(Exception::class);
});

it('stores global cache in a file named global.json', function () {
    $this->cache->put(null, null, ['key' => 'value']);

    expect(file_exists($this->cachePath.DIRECTORY_SEPARATOR.'global.json'))->toBeTrue();
});

it('returns null when cache is disabled', function () {
    config(['pico-settings.cache.enabled' => false]);
    $cache = new JsonSettingsCache;

    $cache->put(null, null, ['theme' => 'dark']);

    expect($cache->get(null, null))->toBeNull();
});

it('skips writing when cache is disabled', function () {
    config(['pico-settings.cache.enabled' => false]);
    $cache = new JsonSettingsCache;

    $cache->put(null, null, ['theme' => 'dark']);

    expect(file_exists($this->cachePath.DIRECTORY_SEPARATOR.'global.json'))->toBeFalse();
});
