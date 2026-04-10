<?php

namespace Pico\Settings\Repositories;

use Pico\Settings\Cache\JsonSettingsCache;
use Pico\Settings\Contracts\SettingsRepositoryInterface;
use Pico\Settings\Models\Setting;

class CachedSettingsRepository implements SettingsRepositoryInterface
{
    public function __construct(private readonly JsonSettingsCache $cache) {}

    /**
     * {@inheritdoc}
     */
    public function get(
        string|array $key,
        mixed $default,
        ?int $userId,
        ?string $model,
    ): mixed {
        $cached = $this->loadFromCache($userId, $model);

        if (is_string($key)) {
            return $cached[$key] ?? $default;
        }

        // Multiple keys — return an associative array, filling missing with default.
        return array_reduce(
            $key,
            fn (array $carry, string $k) => [...$carry, $k => $cached[$k] ?? $default],
            [],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function set(
        string|array $key,
        mixed $value,
        ?int $userId,
        ?string $model,
    ): void {
        $pairs = is_string($key) ? [$key => $value] : $key;

        foreach ($pairs as $k => $v) {
            Setting::updateOrCreate(
                ['user_id' => $userId, 'model' => $model, 'key' => $k],
                ['value' => $v],
            );
        }

        // Invalidate and rebuild cache for this scope.
        $this->rebuildCache($userId, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        string|array|null $key,
        ?int $userId,
        ?string $model,
    ): void {
        $query = Setting::query()
            ->where('user_id', $userId)
            ->where('model', $model);

        match (true) {
            is_null($key)   => $query->delete(),
            is_array($key)  => $query->whereIn('key', $key)->delete(),
            default         => $query->where('key', $key)->delete(),
        };

        $this->rebuildCache($userId, $model);
    }

    /**
     * Load all settings for a scope, hydrating cache on miss.
     *
     * @return array<string, mixed>
     */
    private function loadFromCache(?int $userId, ?string $model): array
    {
        $cached = $this->cache->get($userId, $model);

        if ($cached !== null) {
            return $cached;
        }

        return $this->rebuildCache($userId, $model);
    }

    /**
     * Fetch all DB rows for a scope, write to cache, and return as map.
     *
     * @return array<string, mixed>
     */
    private function rebuildCache(?int $userId, ?string $model): array
    {
        $rows = Setting::query()
            ->where('user_id', $userId)
            ->where('model', $model)
            ->pluck('value', 'key')
            ->all();

        $this->cache->put($userId, $model, $rows);

        return $rows;
    }
}
