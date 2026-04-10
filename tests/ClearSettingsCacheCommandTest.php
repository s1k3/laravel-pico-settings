<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->cachePath = storage_path('app/pico-settings');
    config()->set('pico-settings.cache.path', $this->cachePath);
    File::deleteDirectory($this->cachePath);
});

afterEach(function () {
    File::deleteDirectory($this->cachePath);
});

it('deletes all json cache files', function () {
    File::ensureDirectoryExists($this->cachePath);
    File::put($this->cachePath.'/global.json', '{}');
    File::put($this->cachePath.'/user_1.json', '{}');
    File::put($this->cachePath.'/model_users.json', '{}');

    $this->artisan('pico:clear')
        ->expectsOutput('Deleted 3 cache file(s).')
        ->assertExitCode(0);

    expect(File::glob($this->cachePath.'/*.json'))->toBeEmpty();
});

it('reports when cache is already empty', function () {
    File::ensureDirectoryExists($this->cachePath);

    $this->artisan('pico:clear')
        ->expectsOutput('Cache is already empty.')
        ->assertExitCode(0);
});

it('reports when no cache directory exists', function () {
    $this->artisan('pico:clear')
        ->expectsOutput('No cache directory found. Nothing to clear.')
        ->assertExitCode(0);
});

it('does not delete non-json files', function () {
    File::ensureDirectoryExists($this->cachePath);
    File::put($this->cachePath.'/global.json', '{}');
    File::put($this->cachePath.'/notes.txt', 'keep me');

    $this->artisan('pico:clear')
        ->expectsOutput('Deleted 1 cache file(s).')
        ->assertExitCode(0);

    expect(File::exists($this->cachePath.'/notes.txt'))->toBeTrue();
});
