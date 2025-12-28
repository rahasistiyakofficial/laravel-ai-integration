<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Rahasistiyak\LaravelAiIntegration\Facades\AI;
use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;
use Rahasistiyak\LaravelAiIntegration\Services\EmbeddingService;
use Mockery;

class EmbeddingServiceTest extends TestCase
{
    public function test_service_resolves()
    {
        $this->assertInstanceOf(EmbeddingService::class, AI::embed());
    }

    public function test_generate_delegates_to_driver()
    {
        $mockDriver = Mockery::mock(\Rahasistiyak\LaravelAiIntegration\Drivers\OpenAIDriver::class);
        $mockDriver->shouldReceive('embed')
            ->once()
            ->with('test')
            ->andReturn([0.1, 0.2]);

        $manager = AI::getFacadeRoot();
        $manager->extend('openai', function () use ($mockDriver) {
            return $mockDriver;
        });

        $embedding = AI::embed()->generate('test', 'openai');

        $this->assertEquals([0.1, 0.2], $embedding);
    }
}
