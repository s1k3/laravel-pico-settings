<?php

namespace Pico\Settings\Repositories;

use Pico\Settings\Cache\JsonSettingsCache;
use Pico\Settings\Contracts\SettingsRepositoryInterface;
use Pico\Settings\Models\Setting;

class CachedSettingsRepository implements SettingsRepositoryInterface
{
    public function __construct(private readonly JsonSettingsCache $cache) {}

    public function get(string|array $key, mixed $default, ?int $userId, ?string $model): mixed
    {
        $cached = $this->loadFromCache($userId, $model);

        if (is_string($key)) return $cached[$key] ?? $default;

        return array_reduce($key, fn (array $carry, string $k) => [...$carry, $k => $cached[$k] ?? $default], []);
    }

    public function set(string|array $key, mixed $value, ?int $userId, ?string $model): void
    {
        foreach (is_string($key) ? [$key => $value] : $key as $k => $v) {
            Setting::updateOrCreate(['user_id' => $userId, 'model' => $model, 'key' => $k], ['value' => $v]);
        }

        $this->rebuildCache($userId, $model);
    }

    public function delete(string|array|null $key, ?int $userId, ?string $model): void
    {
        $query = Setting::query()->where('user_id', $userId)->where('model', $model);

        match (true) {
            is_null($key)  => $query->delete(),
            is_array($key) => $query->whereIn('key', $key)->delete(),
            default        => $query->where('key', $key)->delete(),
        };

        $this->rebuildCache($userId, $model);
    }

    private function loadFromCache(?int $userId, ?string $model): array
    {
        return $this->cache->get($userId, $model) ?? $this->rebuildCache($userId, $model);
    }

    private function rebuildCache(?int $userId, ?string $model): array
    {
        $rows = Setting::query()->where('user_id', $userId)->where('model', $model)->pluck('value', 'key')->all();

        $this->cache->put($userId, $model, $rows);

        return $rows;
    }
}
