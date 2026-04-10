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
        return config('pico-settings.cache.enabled', true);
    }


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

        return match (true) {
            is_array($data) => $data,
            default => null,
        };
    }


    public function put(?int $userId, ?string $model, array $data): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        File::ensureDirectoryExists($this->basePath);
        File::put($this->resolvePath($userId, $model), json_encode($data, JSON_PRETTY_PRINT));
    }

    public function forget(?int $userId, ?string $model): void
    {
        $path = $this->resolvePath($userId, $model);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function resolvePath(?int $userId, ?string $model): string
    {

        if (! class_exists($model)) {
            throw new \InvalidArgumentException("Model class [{$model}] does not exist.");
        }

        $tableName = (new $model)->getTable();


        $segment = match (true) {
            $userId !== null && $model !== null => "user_{$userId}_model_".$tableName,
            $userId !== null                   => "user_{$userId}",
            $model !== null                    => 'model_'.$tableName,
            default                            => 'global',
        };

        return $this->basePath.DIRECTORY_SEPARATOR.$segment.'.json';
    }
}
