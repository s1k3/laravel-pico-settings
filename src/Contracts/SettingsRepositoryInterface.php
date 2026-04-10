<?php

namespace Pico\Settings\Contracts;

interface SettingsRepositoryInterface
{
    /**
     * Get a single value or multiple values from storage.
     *
     * @param  string|array<int,string>  $key
     */
    public function get(
        string|array $key,
        mixed $default,
        ?int $userId,
        ?string $model,
    ): mixed;

    /**
     * Set a single key=>value pair or multiple pairs in storage.
     *
     * @param  string|array<string,mixed>  $key
     */
    public function set(
        string|array $key,
        mixed $value,
        ?int $userId,
        ?string $model,
    ): void;

    /**
     * Delete one or more keys from storage.
     * Passing null deletes every key in the scope.
     *
     * @param  string|array<int,string>|null  $key
     */
    public function delete(
        string|array|null $key,
        ?int $userId,
        ?string $model,
    ): void;
}
