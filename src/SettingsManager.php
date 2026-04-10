<?php

namespace Pico\Settings;

use Illuminate\Database\Eloquent\Model;
use Pico\Settings\Contracts\SettingsRepositoryInterface;

/**
 * Entry point — creates a fresh SettingsBuilder for each call chain.
 */
class SettingsManager
{
    public function __construct(private readonly SettingsRepositoryInterface $repository) {}

    /**
     * Scope to a user and return a builder.
     */
    public function for(Model $user): SettingsBuilder
    {
        return (new SettingsBuilder($this->repository))->for($user);
    }

    /**
     * Scope to a model class and return a builder.
     */
    public function model(Model|string $model): SettingsBuilder
    {
        return (new SettingsBuilder($this->repository))->model($model);
    }

    /**
     * Get a setting without any scope (global).
     *
     * @param  string|array<int,string>  $key
     */
    public function get(string|array $key, mixed $default = null): mixed
    {
        return (new SettingsBuilder($this->repository))->get($key, $default);
    }

    /**
     * Set a setting without any scope (global).
     *
     * @param  string|array<string,mixed>  $key
     */
    public function set(string|array $key, mixed $value = null): void
    {
        (new SettingsBuilder($this->repository))->set($key, $value);
    }

    /**
     * Delete one or more global keys. Pass null to wipe all global settings.
     *
     * @param  string|array<int,string>|null  $key
     */
    public function delete(string|array|null $key = null): void
    {
        (new SettingsBuilder($this->repository))->delete($key);
    }
}
