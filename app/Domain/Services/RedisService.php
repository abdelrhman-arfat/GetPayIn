<?php

namespace App\Domain\Services;

use App\Domain\Interfaces\RedisInterface;
use Illuminate\Support\Facades\Redis;

class RedisService implements RedisInterface
{
    public function set(string $key, mixed $value): void
    {
        Redis::set($key, $value);
    }

    public function get(string $key): mixed
    {
        return Redis::get($key);
    }

    public function delete(string $key): void
    {
        Redis::del($key);
    }

    public function exists(string $key): bool
    {
        return Redis::exists($key) > 0;
    }

    public function flush(): void
    {
        Redis::flushall();
    }

    public function __call(string $name, array $arguments): mixed
    {
        return Redis::$name(...$arguments);
    }
}
