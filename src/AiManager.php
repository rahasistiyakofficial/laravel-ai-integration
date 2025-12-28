<?php

namespace Rahasistiyak\LaravelAiIntegration;

use Illuminate\Support\Manager;
use Rahasistiyak\LaravelAiIntegration\Drivers\OpenAIDriver;

class AiManager extends Manager
{
    public function getDefaultDriver()
    {
        return $this->config->get('ai.default', 'openai');
    }

    public function createOpenaiDriver()
    {
        $config = $this->config->get('ai.providers.openai');

        return new OpenAIDriver($config);
    }

    public function createAnthropicDriver()
    {
        $config = $this->config->get('ai.providers.anthropic', []);

        return new Drivers\AnthropicDriver($config);
    }

    public function createOllamaDriver()
    {
        $config = $this->config->get('ai.providers.ollama');

        return new Drivers\OllamaDriver($config);
    }

    public function createGoogleDriver()
    {
        $config = $this->config->get('ai.providers.google', []);

        return new Drivers\GoogleAIDriver($config);
    }

    public function createGroqDriver()
    {
        $config = $this->config->get('ai.providers.groq', []);

        return new Drivers\GroqDriver($config);
    }
}
