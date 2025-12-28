<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Rahasistiyak\LaravelAiIntegration\Facades\AI;
use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;
use Rahasistiyak\LaravelAiIntegration\Tasks\ClassificationTask;
use Mockery;

class ClassificationTaskTest extends TestCase
{
    public function test_task_execution()
    {
        // Mock ChatService
        $mockChat = Mockery::mock(\Rahasistiyak\LaravelAiIntegration\Services\ChatService::class);
        $mockChat->shouldReceive('messages')->once()->andReturnSelf();
        $mockChat->shouldReceive('get')->once()->andReturn(new \Rahasistiyak\LaravelAiIntegration\Models\ChatResponse([
            'choices' => [['message' => ['content' => 'Positive']]]
        ]));

        // Bind mock to ai.chat if we were using facade, but BaseTask takes service in constructor
        // So we need to ensure AI::chat() returns our mock, or we use dependency injection manually

        $task = new ClassificationTask($mockChat);
        $result = $task->execute('I love this!', ['labels' => ['Positive', 'Negative']]);

        $this->assertEquals('Positive', $result);
    }

    public function test_service_execution()
    {
        $mockChat = Mockery::mock(\Rahasistiyak\LaravelAiIntegration\Services\ChatService::class);
        $mockChat->shouldReceive('messages')->once()->andReturnSelf();
        $mockChat->shouldReceive('get')->once()->andReturn(new \Rahasistiyak\LaravelAiIntegration\Models\ChatResponse([
            'choices' => [['message' => ['content' => 'Technology']]]
        ]));

        $this->app->instance(\Rahasistiyak\LaravelAiIntegration\Services\ChatService::class, $mockChat);

        $result = AI::task()->classify('AI is great', ['Technology', 'Sports']);
        $this->assertEquals('Technology', $result);
    }
}
