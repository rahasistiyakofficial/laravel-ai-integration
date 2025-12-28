<?php

namespace Rahasistiyak\LaravelAiIntegration\Services;

use Rahasistiyak\LaravelAiIntegration\AiManager;

class ImageService
{
    protected AiManager $manager;

    public function __construct(AiManager $manager)
    {
        $this->manager = $manager;
    }

    public function generate(string $prompt, array $parameters = []): array
    {
        // For now, simpler than ChatService. Just delegate to driver.
        return $this->manager->driver()->generateImage($prompt, $parameters);
    }
}
