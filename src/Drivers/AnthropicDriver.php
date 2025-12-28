<?php

namespace Rahasistiyak\LaravelAiIntegration\Drivers;

use Rahasistiyak\LaravelAiIntegration\Models\ChatResponse;

class AnthropicDriver extends AbstractDriver
{
    public function chat(array $messages, array $parameters = [])
    {
        $model = $parameters['model'] ?? ($this->config['app_name'] ?? 'claude-3-opus-20240229');

        // Anthropic requires a different message format usually, but we'll assume standard format normalization happening in Service or here.
        // For now, mapping directly.
        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => 1024,
        ], $parameters);

        // Anthropic headers need x-api-key
        $response = $this->getRequest()->withHeaders([
            'x-api-key' => $this->config['api_key'],
            'anthropic-version' => '2023-06-01'
        ])->post($this->getBaseUrl() . '/messages', $payload);

        $response->throw();

        $data = $response->json();

        return new ChatResponse([
            'choices' => [
                [
                    'message' => [
                        'content' => $data['content'][0]['text'] ?? '',
                        'role' => 'assistant'
                    ]
                ]
            ]
        ]);
    }

    public function embed(string $text): array
    {
        // Anthropic doesn't have an embedding API currently (publicly common usage usually relies on others, but assuming sticking to interface).
        // Or if they added it recently, we'd implement here.
        // For now, throwing not supported or returning empty.
        throw new \RuntimeException('Embeddings not supported by Anthropic driver yet.');
    }

    public function generateImage(string $prompt, array $parameters = [])
    {
        throw new \RuntimeException('Image generation not supported by Anthropic driver.');
    }

    protected function buildHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
