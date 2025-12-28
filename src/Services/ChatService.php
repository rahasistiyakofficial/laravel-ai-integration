<?php

namespace Rahasistiyak\LaravelAiIntegration\Services;

use Rahasistiyak\LaravelAiIntegration\AiManager;
use Rahasistiyak\LaravelAiIntegration\Models\ChatResponse;

class ChatService
{
    protected AiManager $manager;
    protected ?string $currentModel = null;
    protected array $messages = [];
    protected array $parameters = [];

    public function __construct(AiManager $manager)
    {
        $this->manager = $manager;
    }

    public function model(string $model): self
    {
        $this->currentModel = $model;
        return $this;
    }

    public function messages(array $messages): self
    {
        $this->messages = $messages;
        return $this;
    }

    public function withParameters(array $parameters): self
    {
        $this->parameters = array_merge($this->parameters, $parameters);
        return $this;
    }

    public function withTools(array $tools): self
    {
        $this->parameters['tools'] = $tools;
        return $this;
    }

    public function stream(callable $callback)
    {
        $driver = $this->manager->driver();

        return $driver->stream($this->messages, $this->parameters, $callback);
    }

    public function get(): ChatResponse
    {
        // TODO: Implement advanced provider selection based on model
        $driver = $this->manager->driver();

        $params = $this->parameters;
        if ($this->currentModel) {
            $params['model'] = $this->currentModel;
        }

        return $driver->chat($this->messages, $params);
    }
}
