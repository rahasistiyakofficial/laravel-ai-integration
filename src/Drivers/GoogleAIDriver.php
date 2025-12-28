<?php

namespace Rahasistiyak\LaravelAiIntegration\Drivers;

use Rahasistiyak\LaravelAiIntegration\Models\ChatResponse;

class GoogleAIDriver extends AbstractDriver
{
    public function chat(array $messages, array $parameters = [])
    {
        $model = $parameters['model'] ?? 'gemini-pro';

        // Google Gemini format is different (parts { text: "..." })
        $contents = array_map(function ($msg) {
            return [
                'role' => $msg['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg['content']]],
            ];
        }, $messages);

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $this->config['api_key'];

        $response = $this->getRequest()->post($url, [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $parameters['temperature'] ?? 0.7,
                'maxOutputTokens' => $parameters['max_tokens'] ?? 1000,
            ]
        ]);

        $response->throw();

        $data = $response->json();

        return new ChatResponse([
            'choices' => [
                [
                    'message' => [
                        'content' => $data['candidates'][0]['content']['parts'][0]['text'] ?? '',
                        'role' => 'assistant'
                    ]
                ]
            ]
        ]);
    }

    public function embed(string $text): array
    {
        $model = 'embedding-001';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:embedContent?key=" . $this->config['api_key'];

        $response = $this->getRequest()->post($url, [
            'model' => "models/{$model}",
            'content' => [
                'parts' => [['text' => $text]]
            ]
        ]);

        $response->throw();

        return $response->json('embedding.values') ?? [];
    }

    public function generateImage(string $prompt, array $parameters = [])
    {
        throw new \RuntimeException('Image generation not supported by Google driver yet.');
    }
}
