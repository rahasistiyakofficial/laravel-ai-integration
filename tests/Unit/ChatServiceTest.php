<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Rahasistiyak\LaravelAiIntegration\Services\ChatService;
use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;
use Mockery;

class ChatServiceTest extends TestCase
{
    public function test_with_tools()
    {
        $mockDriver = Mockery::mock(\Rahasistiyak\LaravelAiIntegration\Drivers\OpenAIDriver::class);
        $mockDriver->shouldReceive('chat')
            ->once()
            ->withArgs(function ($messages, $parameters) {
                return isset($parameters['tools']);
            })
            ->andReturn(new \Rahasistiyak\LaravelAiIntegration\Models\ChatResponse(['choices' => []]));

        $manager = \Rahasistiyak\LaravelAiIntegration\Facades\AI::getFacadeRoot();
        $manager->extend('openai', function () use ($mockDriver) {
            return $mockDriver;
        });

        $service = new ChatService($manager);
        $service->withTools([['type' => 'function']]);
        $service->get();
    }

    public function test_stream_delegates_to_driver()
    {
        $mockDriver = Mockery::mock(\Rahasistiyak\LaravelAiIntegration\Drivers\OpenAIDriver::class);
        $mockDriver->shouldReceive('stream')
            ->once()
            ->withArgs(function ($messages, $parameters, $callback) {
                $callback('test chunk');
                return true;
            });

        $manager = \Rahasistiyak\LaravelAiIntegration\Facades\AI::getFacadeRoot();
        $manager->extend('openai', function () use ($mockDriver) {
            return $mockDriver;
        });

        $service = new ChatService($manager);
        $service->messages([['role' => 'user', 'content' => 'test']]);

        $chunks = [];
        $service->stream(function ($chunk) use (&$chunks) {
            $chunks[] = $chunk;
        });

        $this->assertContains('test chunk', $chunks);
    }
}
