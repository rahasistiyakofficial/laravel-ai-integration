<?php

namespace Rahasistiyak\LaravelAiIntegration\Services;

use Illuminate\Contracts\Container\Container;

class TaskService
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function classify(string $text, array $labels = []): string
    {
        return $this->container->make(\Rahasistiyak\LaravelAiIntegration\Tasks\ClassificationTask::class)
            ->execute($text, ['labels' => $labels]);
    }
}
