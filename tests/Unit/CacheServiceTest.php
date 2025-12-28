<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Illuminate\Support\Facades\Cache;
use Rahasistiyak\LaravelAiIntegration\Services\CacheService;
use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;

class CacheServiceTest extends TestCase
{
    protected CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();

        config(['ai.cache.enabled' => true]);
        config(['ai.cache.driver' => 'array']);
        config(['ai.cache.ttl' => 3600]);

        $this->cacheService = new CacheService();
        Cache::flush();
    }

    public function test_generate_key_is_deterministic()
    {
        $messages = [['role' => 'user', 'content' => 'Hello']];
        $params = ['temperature' => 0.7, 'max_tokens' => 100];

        $key1 = $this->cacheService->generateKey('openai', $messages, $params);
        $key2 = $this->cacheService->generateKey('openai', $messages, $params);

        $this->assertEquals($key1, $key2);
    }

    public function test_cache_put_and_get()
    {
        $key = 'test:key';
        $value = ['content' => 'Test response'];

        $this->cacheService->put($key, $value);
        $result = $this->cacheService->get($key);

        $this->assertEquals($value, $result);
    }

    public function test_cache_has()
    {
        $key = 'test:key';

        $this->assertFalse($this->cacheService->has($key));

        $this->cacheService->put($key, 'value');

        $this->assertTrue($this->cacheService->has($key));
    }

    public function test_cache_forget()
    {
        $key = 'test:key';
        $this->cacheService->put($key, 'value');

        $this->assertTrue($this->cacheService->has($key));

        $this->cacheService->forget($key);

        $this->assertFalse($this->cacheService->has($key));
    }

    public function test_record_hit_and_miss()
    {
        $this->cacheService->recordHit();
        $this->cacheService->recordHit();
        $this->cacheService->recordMiss();

        $stats = $this->cacheService->stats();

        $this->assertEquals(66.67, $stats['hit_rate']); // 2 hits, 1 miss = 66.67%
    }

    public function test_cache_disabled()
    {
        config(['ai.cache.enabled' => false]);
        $service = new CacheService();

        $key = 'test:key';
        $service->put($key, 'value');

        $this->assertNull($service->get($key));
        $this->assertFalse($service->has($key));
    }
}
