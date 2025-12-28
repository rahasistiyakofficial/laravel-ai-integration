<?php

namespace Rahasistiyak\LaravelAiIntegration\Services;

use Rahasistiyak\LaravelAiIntegration\AiManager;

class EmbeddingService
{
    protected AiManager $manager;

    public function __construct(AiManager $manager)
    {
        $this->manager = $manager;
    }

    public function generate(string $text, $provider = null): array
    {
        return $this->manager->driver($provider)->embed($text);
    }
}
