<?php

namespace Rahasistiyak\LaravelAiIntegration\Contracts;

use Rahasistiyak\LaravelAiIntegration\Models\ChatResponse;

interface AiProviderInterface
{
    public function chat(array $messages, array $parameters = []);

    public function embed(string $text): array;

    public function generateImage(string $prompt, array $parameters = []);

    public function stream(array $messages, array $parameters = [], callable $callback);
}
