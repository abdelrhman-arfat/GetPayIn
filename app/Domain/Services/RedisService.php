<?php

namespace App\Domain\Services;

use App\Domain\Interfaces\RedisInterface;
use Illuminate\Support\Facades\Redis as RedisFacade;

class RedisService implements RedisInterface
{
    public function set(string $key, mixed $value): void
    {
        RedisFacade::set($key, $value);
    }

    public function remember(string $key, ?int $ttl = null, callable $callback)
    {
        if ($this->exists($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }


    public function get(string $key): mixed
    {
        return RedisFacade::get($key);
    }

    public function delete(string $key): void
    {
        RedisFacade::del($key);
    }

    public function exists(string $key): bool
    {
        return RedisFacade::exists($key) > 0;
    }

    public function flush(): void
    {
        RedisFacade::flushall();
    }

    public function __call(string $name, array $arguments): mixed
    {
        return RedisFacade::$name(...$arguments);
    }
}
