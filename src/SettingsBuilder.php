<?php

namespace Pico\Settings;

use Illuminate\Database\Eloquent\Model;
use Pico\Settings\Contracts\SettingsRepositoryInterface;

class SettingsBuilder
{
    private ?int $userId = null;
    private ?string $modelClass = null;

    public function __construct(private readonly SettingsRepositoryInterface $repository) {}

    /**
     * Scope settings to a specific user.
     */
    public function for(Model $user): static
    {
        $clone = clone $this;
        $clone->userId = $user->getKey();

        return $clone;
    }

    /**
     * Scope settings to a specific model class.
     */
    public function model(Model|string $model): static
    {
        $clone = clone $this;
        $clone->modelClass = is_string($model) ? $model : $model::class;

        return $clone;
    }

    /**
     * Get one or more settings values.
     *
     * @param  string|array<int,string>  $key
     */
    public function get(string|array $key, mixed $default = null): mixed
    {
        return $this->repository->get($key, $default, $this->userId, $this->modelClass);
    }

    /**
     * Set one or more settings values.
     *
     * @param  string|array<string,mixed>  $key
     */
    public function set(string|array $key, mixed $value = null): void
    {
        $this->repository->set($key, $value, $this->userId, $this->modelClass);
    }

    /**
     * Delete one or more keys. Pass null to wipe the entire scope.
     *
     * @param  string|array<int,string>|null  $key
     */
    public function delete(string|array|null $key = null): void
    {
        $this->repository->delete($key, $this->userId, $this->modelClass);
    }
}
