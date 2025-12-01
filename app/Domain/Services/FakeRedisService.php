<?php

namespace App\Domain\Services;

use App\Domain\Interfaces\RedisInterface;

class FakeRedisService implements RedisInterface
{
  private array $store = [];

  public function set(string $key, mixed $value, ?int $ttl = null): void
  {
    $this->store[$key] = $value;
  }

  public function remember(string $key, ?int $ttl = null, callable $callback)
  {
    if (isset($this->store[$key])) return $this->store[$key];
    $value = $callback();
    $this->store[$key] = $value;
    return $value;
  }

  public function get(string $key): mixed
  {
    return $this->store[$key] ?? null;
  }

  public function delete(string $key): void
  {
    unset($this->store[$key]);
  }

  public function exists(string $key): bool
  {
    return isset($this->store[$key]);
  }

  public function flush(): void
  {
    $this->store = [];
  }

  public function __call(string $name, array $arguments): mixed
  {
    return null;
  }
}
