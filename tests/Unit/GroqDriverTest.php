<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Rahasistiyak\LaravelAiIntegration\Drivers\GroqDriver;
use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;

class GroqDriverTest extends TestCase
{
    public function test_chat_completion()
    {
        Http::fake([
            'api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Hello from Groq',
                            'role' => 'assistant'
                        ]
                    ]
                ]
            ]),
        ]);

        $config = ['api_key' => 'test', 'base_url' => 'https://api.groq.com/openai/v1'];
        $driver = new GroqDriver($config);
        $response = $driver->chat([['role' => 'user', 'content' => 'Hello']]);

        $this->assertEquals('Hello from Groq', $response->content());
    }

    public function test_embed_throws_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Groq driver does not support embeddings yet.');

        $driver = new GroqDriver(['api_key' => 'test']);
        $driver->embed('test');
    }
}
