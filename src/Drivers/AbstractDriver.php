<?php

namespace Rahasistiyak\LaravelAiIntegration\Drivers;

use Illuminate\Support\Facades\Http;
use Rahasistiyak\LaravelAiIntegration\Contracts\AiProviderInterface;

abstract class AbstractDriver implements AiProviderInterface
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    abstract public function chat(array $messages, array $parameters = []);

    public function embed(string $text): array
    {
        throw new \Exception("Driver does not support embeddings.");
    }

    public function generateImage(string $prompt, array $parameters = [])
    {
        throw new \Exception("Driver does not support image generation.");
    }

    public function stream(array $messages, array $parameters = [], callable $callback)
    {
        throw new \Exception("Driver does not support streaming.");
    }

    protected function buildHeaders(): array
    {
        return [
            'Authorization' => "Bearer " . ($this->config['api_key'] ?? ''),
            'Content-Type' => 'application/json',
        ];
    }

    protected function getBaseUrl(): string
    {
        return rtrim($this->config['base_url'] ?? '', '/');
    }

    protected function getRequest()
    {
        return Http::withHeaders($this->buildHeaders())
            ->timeout($this->config['timeout'] ?? 30);
    }
}
