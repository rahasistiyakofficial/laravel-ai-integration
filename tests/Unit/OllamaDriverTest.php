<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Rahasistiyak\LaravelAiIntegration\Drivers\OllamaDriver;
use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;

class OllamaDriverTest extends TestCase
{
    public function test_chat_completion()
    {
        Http::fake([
            'localhost:11434/api/chat' => Http::response([
                'message' => [
                    'content' => 'Hello from Ollama',
                    'role' => 'assistant'
                ]
            ]),
        ]);

        $config = ['base_url' => 'http://localhost:11434'];
        $driver = new OllamaDriver($config);
        $response = $driver->chat([['role' => 'user', 'content' => 'Hello']]);

        $this->assertEquals('Hello from Ollama', $response->content());
    }

    public function test_embeddings()
    {
        Http::fake([
            'localhost:11434/api/embeddings' => Http::response([
                'embedding' => [0.1, 0.2, 0.3]
            ]),
        ]);

        $config = ['base_url' => 'http://localhost:11434'];
        $driver = new OllamaDriver($config);
        $embedding = $driver->embed('test');

        $this->assertCount(3, $embedding);
        $this->assertEquals(0.1, $embedding[0]);
    }
}
