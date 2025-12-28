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
        $startTime = microtime(true);
        $driver = $this->manager->driver();
        $params = $this->parameters;

        if ($this->currentModel) {
            $params['model'] = $this->currentModel;
        }

        // Check cache first
        $cacheService = app(\Rahasistiyak\LaravelAiIntegration\Services\CacheService::class);
        $cacheKey = $cacheService->generateKey(
            $this->manager->getDefaultDriver(),
            $this->messages,
            $params
        );

        $cached = false;
        if ($cacheService->has($cacheKey)) {
            $cacheService->recordHit();
            $response = new ChatResponse($cacheService->get($cacheKey));
            $cached = true;
        } else {
            $cacheService->recordMiss();
            $response = $driver->chat($this->messages, $params);

            // Cache the response
            $cacheService->put($cacheKey, $response->toArray());
        }

        $duration = (int) ((microtime(true) - $startTime) * 1000);

        // Track request if tracking is enabled
        if (config('ai.features.tracking.enabled', false)) {
            $this->trackRequest($response, $params, $duration, $cached);
        }

        return $response;
    }

    /**
     * Track AI request for analytics and cost calculation
     */
    protected function trackRequest(ChatResponse $response, array $params, int $duration, bool $cached): void
    {
        $provider = $this->manager->getDefaultDriver();
        $model = $params['model'] ?? 'unknown';

        $tokens = \Rahasistiyak\LaravelAiIntegration\Support\TokenCounter::calculateRequestTokens(
            $this->messages,
            $response->content()
        );

        $cost = \Rahasistiyak\LaravelAiIntegration\Support\CostCalculator::calculate(
            $provider,
            $model,
            $tokens['input_tokens'],
            $tokens['output_tokens']
        );

        \Rahasistiyak\LaravelAiIntegration\Models\AiRequest::create([
            'provider' => $provider,
            'model' => $model,
            'messages' => $this->messages,
            'response' => $response->content(),
            'input_tokens' => $tokens['input_tokens'],
            'output_tokens' => $tokens['output_tokens'],
            'total_tokens' => $tokens['total_tokens'],
            'cost' => $cost,
            'duration_ms' => $duration,
            'cached' => $cached,
        ]);
    }
}
