<?php

namespace Rahasistiyak\LaravelAiIntegration\Drivers;

use Rahasistiyak\LaravelAiIntegration\Models\ChatResponse;

class OpenAIDriver extends AbstractDriver
{
    public function chat(array $messages, array $parameters = [])
    {
        $model = $parameters['model'] ?? ($this->config['models']['chat'][0] ?? 'gpt-3.5-turbo');

        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ], $parameters);

        if (isset($parameters['tools'])) {
            $payload['tools'] = $parameters['tools'];
        }

        $response = $this->getRequest()->post($this->getBaseUrl() . '/chat/completions', $payload);

        $response->throw();

        return new ChatResponse($response->json());
    }

    public function stream(array $messages, array $parameters = [], callable $callback)
    {
        $model = $parameters['model'] ?? ($this->config['models']['chat'][0] ?? 'gpt-3.5-turbo');

        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
            'stream' => true,
        ], $parameters);

        $response = $this->getRequest()->post($this->getBaseUrl() . '/chat/completions', [
            'json' => $payload,
            'stream' => true,
        ]);

        $body = $response->toPsrResponse()->getBody();

        while (!$body->eof()) {
            $line = \Rahasistiyak\LaravelAiIntegration\Support\StreamParser::readLine($body);
            if (empty($line))
                continue;

            if (str_starts_with($line, 'data: ')) {
                $data = substr($line, 6);
                if (trim($data) === '[DONE]')
                    break;

                $json = json_decode($data, true);
                if (isset($json['choices'][0]['delta']['content'])) {
                    $callback($json['choices'][0]['delta']['content']);
                }
            }
        }
    }

    public function embed(string $text): array
    {
        $model = $this->config['models']['embedding'][0] ?? 'text-embedding-ada-002';

        $response = $this->getRequest()->post($this->getBaseUrl() . '/embeddings', [
            'model' => $model,
            'input' => $text,
        ]);

        $response->throw();

        return $response->json('data.0.embedding') ?? [];
    }

    public function generateImage(string $prompt, array $parameters = [])
    {
        // TODO: Implement image generation
        return [];
    }
}
