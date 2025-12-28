<?php

namespace Rahasistiyak\LaravelAiIntegration\Drivers;

use Rahasistiyak\LaravelAiIntegration\Models\ChatResponse;

class GroqDriver extends AbstractDriver
{
    public function chat(array $messages, array $parameters = [])
    {
        $model = $parameters['model'] ?? ($this->config['models']['chat'][0] ?? 'mixtral-8x7b-32768');

        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ], $parameters);

        // Groq is compatible with OpenAI API format
        $response = $this->getRequest()->post($this->getBaseUrl() . '/chat/completions', $payload);

        $response->throw();

        return new ChatResponse($response->json());
    }

    public function embed(string $text): array
    {
        // Groq does not support embeddings yet (as of standard implementation), 
        // but if they added it, it would be here. Throwing exception for now.
        throw new \Exception("Groq driver does not support embeddings yet.");
    }

    public function generateImage(string $prompt, array $parameters = [])
    {
        throw new \Exception("Groq driver does not support image generation.");
    }

    public function stream(array $messages, array $parameters = [], callable $callback)
    {
        $model = $parameters['model'] ?? ($this->config['models']['chat'][0] ?? 'mixtral-8x7b-32768');

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
}
