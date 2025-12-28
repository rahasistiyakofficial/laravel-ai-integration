<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests\Unit;

use Rahasistiyak\LaravelAiIntegration\Tests\TestCase;
use Rahasistiyak\LaravelAiIntegration\Facades\AI;
use Mockery;

class ImageServiceTest extends TestCase
{
    public function test_image_generation_delegates()
    {
        $mockDriver = Mockery::mock(\Rahasistiyak\LaravelAiIntegration\Drivers\OpenAIDriver::class);
        $mockDriver->shouldReceive('generateImage')
            ->once()
            ->with('A cute cat', [])
            ->andReturn(['url' => 'http://example.com/cat.jpg']);

        $manager = AI::getFacadeRoot();
        $manager->extend('openai', function () use ($mockDriver) {
            return $mockDriver;
        });

        $result = AI::image()->generate('A cute cat');

        $this->assertEquals(['url' => 'http://example.com/cat.jpg'], $result);
    }
}
