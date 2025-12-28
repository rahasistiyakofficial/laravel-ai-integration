<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Rahasistiyak\LaravelAiIntegration\Drivers\AnthropicDriver;
use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;

class AnthropicDriverTest extends TestCase
{
    public function test_chat_completion()
    {
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'content' => [
                    [
                        'text' => 'Hello from Claude',
                        'type' => 'text'
                    ]
                ]
            ]),
        ]);

        $config = ['api_key' => 'test', 'base_url' => 'https://api.anthropic.com/v1'];
        $driver = new AnthropicDriver($config);
        $response = $driver->chat([['role' => 'user', 'content' => 'Hello']]);

        $this->assertEquals('Hello from Claude', $response->content());
    }
}
