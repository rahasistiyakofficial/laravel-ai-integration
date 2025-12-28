<?php

namespace Rahasistiyak\LaravelAiIntegration\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CacheService
{
    protected string $driver;
    protected int $ttl;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = config('ai.cache.enabled', true);
        $this->driver = config('ai.cache.driver', 'redis');
        $this->ttl = config('ai.cache.ttl', 3600);
    }

    /**
     * Generate cache key from request parameters
     */
    public function generateKey(string $provider, array $messages, array $parameters = []): string
    {
        // Remove non-deterministic parameters
        $deterministicParams = collect($parameters)
            ->except(['stream', 'user', 'n'])
            ->sortKeys()
            ->toArray();

        $payload = [
            'provider' => $provider,
            'messages' => $messages,
            'parameters' => $deterministicParams,
        ];

        return 'ai:' . md5(json_encode($payload));
    }

    /**
     * Get cached response
     */
    public function get(string $key)
    {
        if (!$this->enabled) {
            return null;
        }

        return Cache::store($this->driver)->get($key);
    }

    /**
     * Store response in cache
     */
    public function put(string $key, $value, ?int $ttl = null): void
    {
        if (!$this->enabled) {
            return;
        }

        $ttl = $ttl ?? $this->ttl;
        Cache::store($this->driver)->put($key, $value, $ttl);
    }

    /**
     * Check if response is cached
     */
    public function has(string $key): bool
    {
        if (!$this->enabled) {
            return false;
        }

        return Cache::store($this->driver)->has($key);
    }

    /**
     * Forget cached response
     */
    public function forget(string $key): void
    {
        Cache::store($this->driver)->forget($key);
    }

    /**
     * Clear all AI caches
     */
    public function flush(): void
    {
        // Get all keys starting with 'ai:'
        $keys = Cache::store($this->driver)->get('ai:keys', []);

        foreach ($keys as $key) {
            $this->forget($key);
        }

        Cache::store($this->driver)->forget('ai:keys');
    }

    /**
     * Get cache statistics
     */
    public function stats(): array
    {
        return [
            'enabled' => $this->enabled,
            'driver' => $this->driver,
            'ttl' => $this->ttl,
            'hit_rate' => $this->getHitRate(),
        ];
    }

    /**
     * Calculate cache hit rate
     */
    protected function getHitRate(): float
    {
        $hits = (int) Cache::store($this->driver)->get('ai:cache:hits', 0);
        $misses = (int) Cache::store($this->driver)->get('ai:cache:misses', 0);

        $total = $hits + $misses;

        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    /**
     * Record cache hit
     */
    public function recordHit(): void
    {
        Cache::store($this->driver)->increment('ai:cache:hits');
    }

    /**
     * Record cache miss
     */
    public function recordMiss(): void
    {
        Cache::store($this->driver)->increment('ai:cache:misses');
    }
}
