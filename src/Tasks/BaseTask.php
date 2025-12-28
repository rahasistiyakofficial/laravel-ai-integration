<?php

namespace Rahasistiyak\LaravelAiIntegration\Tasks;

use Rahasistiyak\LaravelAiIntegration\Services\ChatService;

abstract class BaseTask
{
    protected ChatService $ai;

    public function __construct(ChatService $ai)
    {
        $this->ai = $ai;
    }

    abstract public function execute($input, array $options = []);

    protected function buildPrompt(string $template, array $data = []): array
    {
        $content = $template;
        foreach ($data as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }

        return [
            ['role' => 'user', 'content' => $content]
        ];
    }
}
