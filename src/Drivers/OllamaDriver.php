<?php

namespace Rahasistiyak\LaravelAiIntegration\Drivers;

use Rahasistiyak\LaravelAiIntegration\Models\ChatResponse;

class OllamaDriver extends AbstractDriver
{
    public function chat(array $messages, array $parameters = [])
    {
        $model = $parameters['model'] ?? ($this->config['models']['chat'][0] ?? 'llama3');

        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
            'stream' => false,
        ], $parameters);

        $response = $this->getRequest()->post($this->getBaseUrl() . '/api/chat', $payload);

        $response->throw();

        $data = $response->json();

        return new ChatResponse([
            'choices' => [
                [
                    'message' => [
                        'content' => $data['message']['content'] ?? '',
                        'role' => $data['message']['role'] ?? 'assistant'
                    ]
                ]
            ]
        ]);
    }

    public function embed(string $text): array
    {
        $model = $this->config['models']['embedding'][0] ?? 'nomic-embed-text';

        $response = $this->getRequest()->post($this->getBaseUrl() . '/api/embeddings', [
            'model' => $model,
            'prompt' => $text,
        ]);

        $response->throw();

        return $response->json('embedding') ?? [];
    }

    public function generateImage(string $prompt, array $parameters = [])
    {
        throw new \RuntimeException('Image generation not supported by Ollama driver.');
    }
}
