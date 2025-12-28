<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Rahasistiyak\LaravelAiIntegration\Drivers\GoogleAIDriver;
use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;

class GoogleAIDriverTest extends TestCase
{
    public function test_chat_completion()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [['text' => 'Hello from Gemini']]
                        ]
                    ]
                ]
            ]),
        ]);

        $config = ['api_key' => 'test'];
        $driver = new GoogleAIDriver($config);
        $response = $driver->chat([['role' => 'user', 'content' => 'Hello']]);

        $this->assertEquals('Hello from Gemini', $response->content());
    }

    public function test_embeddings()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'embedding' => ['values' => [0.1, 0.2, 0.3]]
            ]),
        ]);

        $config = ['api_key' => 'test'];
        $driver = new GoogleAIDriver($config);
        $embedding = $driver->embed('test');

        $this->assertCount(3, $embedding);
        $this->assertEquals(0.1, $embedding[0]);
    }
}
