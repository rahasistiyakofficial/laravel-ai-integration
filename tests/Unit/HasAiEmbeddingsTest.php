<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;
use Rahasistiyak\LaravelAiIntegration\Traits\HasAiEmbeddings;
use Rahasistiyak\LaravelAiIntegration\Services\EmbeddingService;
use Mockery;

class TestModel
{
    use HasAiEmbeddings;

    public function toJson($options = 0)
    {
        return '{"id":1, "name":"Test"}';
    }
}

class HasAiEmbeddingsTest extends TestCase
{
    public function test_generate_embedding()
    {
        $mockEmbed = Mockery::mock(EmbeddingService::class);
        $mockEmbed->shouldReceive('generate')
            ->once()
            ->with('{"id":1, "name":"Test"}')
            ->andReturn([0.1, 0.2]);

        $this->app->instance('ai.embed', $mockEmbed);

        $model = new TestModel();
        $embedding = $model->generateEmbedding();

        $this->assertEquals([0.1, 0.2], $embedding);
    }
}
