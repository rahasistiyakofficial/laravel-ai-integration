<?php

namespace Rahasistiyak\LaravelAiIntegration;

use Illuminate\Support\ServiceProvider;

class AiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ai.php', 'ai');

        $this->app->singleton('ai.manager', function ($app) {
            return new AiManager($app);
        });

        $this->app->singleton('ai.chat', function ($app) {
            return new Services\ChatService($app['ai.manager']);
        });

        $this->app->singleton('ai.embed', function ($app) {
            return new Services\EmbeddingService($app['ai.manager']);
        });

        $this->app->singleton('ai.task', function ($app) {
            return new Services\TaskService($app);
        });

        $this->app->singleton('ai.image', function ($app) {
            return new Services\ImageService($app['ai.manager']);
        });

        $this->app->singleton(\Rahasistiyak\LaravelAiIntegration\Services\CacheService::class, function ($app) {
            return new Services\CacheService();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ai.php' => config_path('ai.php'),
            ], 'ai-config');

            $this->commands([
                \Rahasistiyak\LaravelAiIntegration\Console\Commands\AiGenerateCodeCommand::class,
                \Rahasistiyak\LaravelAiIntegration\Console\Commands\AiCacheClearCommand::class,
                \Rahasistiyak\LaravelAiIntegration\Console\Commands\AiUsageCommand::class,
                \Rahasistiyak\LaravelAiIntegration\Console\Commands\AiTestCommand::class,
            ]);
        }
    }
}
