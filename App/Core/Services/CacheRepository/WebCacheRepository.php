<?php

declare(strict_types=1);

namespace App\Core\Services\CacheRepository;

use Closure;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Cache\Store;

class WebCacheRepository implements CacheRepository
{
    public function __construct(
        private readonly CacheRepository $repository
    ) {
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key The unique key of this item in the cache.
     * @param null|mixed $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *   MUST be thrown if the $key string is not a legal value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->repository->get($this->alterKey($key), $default);
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string $key The key of the item to store.
     * @param mixed $value The value of the item to store, must be serializable.
     * @param null|\DateInterval|int $ttl Optional. The TTL value of this item. If no value is sent and
     * the driver supports TTL then the library may set a default value
     * for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *   MUST be thrown if the $key string is not a legal value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function set(string $key, mixed $value, int|\DateInterval|null $ttl = null): bool
    {
        return $this->repository->set($this->alterKey($key), $value, $ttl);
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *   MUST be thrown if the $key string is not a legal value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delete(string $key): bool
    {
        return $this->repository->delete($this->alterKey($key));
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear(): bool
    {
        return $this->repository->clear();
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys A list of keys that can obtained in a single operation.
     * @param null|mixed $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getMultiple(iterable $keys, $default = null): iterable
    {
        $altKeys = [];

        foreach ($keys as $key) {
            $altKeys[] = $this->alterKey($key);
        }

        return $this->repository->getMultiple($altKeys, $default);
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable $values A list of key => value pairs for a multiple-set operation.
     * @param null|\DateInterval|int $ttl Optional. The TTL value of this item. If no value is sent and
     * the driver supports TTL then the library may set a default value
     * for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *   MUST be thrown if $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setMultiple(iterable $values, int|\DateInterval|null $ttl = null): bool
    {
        $copy = [];
        foreach ($values as $key => $value) {
            $copy[$this->alterKey($key)] = $value;
        }

        return $this->repository->setMultiple($copy, $ttl);
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $altKeys = [];

        foreach ($keys as $key) {
            $altKeys[] = $this->alterKey($key);
        }

        return $this->repository->deleteMultiple($altKeys);
    }

    /**
     * Determines whether an item is present in the cache.
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *   MUST be thrown if the $key string is not a legal value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function has(string $key): bool
    {
        return $this->repository->has($this->alterKey($key));
    }

    /**
     * @inheritDoc
     */
    public function pull($key, $default = null): mixed
    {
        return $this->repository->pull($this->alterKey($key), $default);
    }

    /**
     * @inheritDoc
     */
    public function put($key, $value, $ttl = null): bool
    {
        return $this->repository->put($this->alterKey($key), $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function add($key, $value, $ttl = null): bool
    {
        return $this->repository->add($this->alterKey($key), $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function increment($key, $value = 1): int | bool
    {
        return $this->repository->increment($this->alterKey($key), $value);
    }

    /**
     * @inheritDoc
     */
    public function decrement($key, $value = 1): int | bool
    {
        return $this->repository->decrement($this->alterKey($key), $value);
    }

    /**
     * @inheritDoc
     */
    public function forever($key, $value): bool
    {
        return $this->repository->forever($this->alterKey($key), $value);
    }

    /**
     * @inheritDoc
     */
    public function remember($key, $ttl, Closure $callback): mixed
    {
        return $this->repository->remember($this->alterKey($key), $ttl, $callback);
    }

    /**
     * @inheritDoc
     */
    public function sear($key, Closure $callback): mixed
    {
        return $this->repository->sear($this->alterKey($key), $callback);
    }

    /**
     * @inheritDoc
     */
    public function rememberForever($key, Closure $callback): mixed
    {
        return $this->repository->rememberForever($this->alterKey($key), $callback);
    }

    /**
     * @inheritDoc
     */
    public function forget($key): bool
    {
        return $this->repository->forget($this->alterKey($key));
    }

    /**
     * @inheritDoc
     *
     * @return \Illuminate\Contracts\Cache\Store
     */
    public function getStore(): Store
    {
        return $this->repository->getStore();
    }

    /**
     * Alter cache key
     *
     * @param string $key
     *
     * @return string
     */
    private function alterKey(string $key): string
    {
        return $key;
    }
}
