<?php

namespace Pico\Settings\Cache;

use Illuminate\Support\Facades\File;

class JsonSettingsCache
{
    private readonly string $basePath;

    public function __construct()
    {
        $this->basePath = config('pico-settings.cache.path', storage_path('app/pico-settings'));
    }

    public function isEnabled(): bool
    {
        return (bool) config('pico-settings.cache.enabled', true);
    }

    /**
     * Retrieve all cached settings for a given scope.
     *
     * @return array<string, mixed>|null  null when cache miss
     */
    public function get(?int $userId, ?string $model): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $path = $this->resolvePath($userId, $model);

        if (! File::exists($path)) {
            return null;
        }

        $data = json_decode(File::get($path), associative: true);

        return is_array($data) ? $data : null;
    }

    /**
     * Write all settings for a given scope to the JSON cache.
     *
     * @param  array<string, mixed>  $data
     */
    public function put(?int $userId, ?string $model, array $data): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        File::ensureDirectoryExists($this->basePath);
        File::put($this->resolvePath($userId, $model), json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Remove the cache file for a given scope.
     */
    public function forget(?int $userId, ?string $model): void
    {
        $path = $this->resolvePath($userId, $model);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function resolvePath(?int $userId, ?string $model): string
    {
        $segment = match (true) {
            $userId !== null && $model !== null => "user_{$userId}_model_".class_basename($model),
            $userId !== null                   => "user_{$userId}",
            $model !== null                    => 'model_'.class_basename($model),
            default                            => 'global',
        };

        return $this->basePath.DIRECTORY_SEPARATOR.$segment.'.json';
    }
}
