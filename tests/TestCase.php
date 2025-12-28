<?php

namespace Rahasistiyak\LaravelAiIntegration\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Rahasistiyak\LaravelAiIntegration\AiServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            AiServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'AI' => \Rahasistiyak\LaravelAiIntegration\Facades\AI::class,
        ];
    }
}
