<?php

namespace Pico\Settings\Contracts;

interface SettingsRepositoryInterface
{
    public function get(string|array $key, mixed $default, ?int $userId, ?string $model): mixed;
    public function set(string|array $key, mixed $value, ?int $userId, ?string $model): void;
    public function delete(string|array|null $key, ?int $userId, ?string $model): void;
}
