<?php

namespace App\Domain\Interfaces;

interface RedisInterface
{
    /**
     * Set a value in Redis.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value): void;

    /**
     * Get a value from Redis.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * Delete a key from Redis.
     *
     * @param string $key
     */
    public function delete(string $key): void;

    /**
     * Check if a key exists in Redis.
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * Flush all keys in Redis.
     */
    public function flush(): void;

    /**
     * Magic method to forward calls to Redis facade.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed;
}
