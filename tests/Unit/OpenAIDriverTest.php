<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Rahasistiyak\LaravelAiIntegration\Drivers\OpenAIDriver;
use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;
use Rahasistiyak\LaravelAiIntegration\Facades\AI;

class OpenAIDriverTest extends TestCase
{
    public function test_chat_completion()
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => ['content' => 'Test response']
                    ]
                ]
            ]),
        ]);

        $config = ['api_key' => 'test', 'base_url' => 'https://api.openai.com/v1'];
        $driver = new OpenAIDriver($config);
        $response = $driver->chat([['role' => 'user', 'content' => 'Hello']]);

        $this->assertEquals('Test response', $response->content());
    }

    public function test_facade_resolves()
    {
        $this->assertInstanceOf(\Rahasistiyak\LaravelAiIntegration\AiManager::class, AI::getFacadeRoot());
    }

    public function test_manager_driver_resolution()
    {
        // Set config
        config(['ai.default' => 'openai']);
        config(['ai.providers.openai' => ['driver' => 'openai', 'api_key' => 'test']]);

        $driver = AI::driver('openai');
        $this->assertInstanceOf(OpenAIDriver::class, $driver);
    }

    public function test_chat_completion_error()
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response('Error', 500),
        ]);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);

        $driver = new OpenAIDriver([
            'api_key' => 'test',
            'base_url' => 'https://api.openai.com/v1'
        ]);
        $driver->chat([['role' => 'user', 'content' => 'Hello']]);
    }
}
