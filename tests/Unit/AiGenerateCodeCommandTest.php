<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;
use Mockery;

class AiGenerateCodeCommandTest extends TestCase
{
    public function test_command_generates_code()
    {
        $mockChat = Mockery::mock(\Rahasistiyak\LaravelAiIntegration\Services\ChatService::class);
        $mockChat->shouldReceive('messages')->once()->andReturnSelf();
        $mockChat->shouldReceive('get')->once()->andReturn(new \Rahasistiyak\LaravelAiIntegration\Models\ChatResponse([
            'choices' => [['message' => ['content' => '<?php echo "Hello"; ?>']]]
        ]));

        $this->app->instance(\Rahasistiyak\LaravelAiIntegration\Services\ChatService::class, $mockChat);
        $this->app->instance('ai.chat', $mockChat);

        $this->artisan('ai:generate-code', ['prompt' => 'Hello World', '--language' => 'php'])
            ->assertExitCode(0)
            ->expectsOutput('<?php echo "Hello"; ?>');
    }
}
