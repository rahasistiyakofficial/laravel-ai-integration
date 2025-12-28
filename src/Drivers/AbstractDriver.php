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

    /**
     * Execute request with retry logic and circuit breaker
     */
    protected function executeWithRetry(callable $callback, int $maxRetries = 3)
    {
        $circuitBreaker = new \Rahasistiyak\LaravelAiIntegration\Support\CircuitBreaker(
            $this->getProviderName(),
            5, // failure threshold
            60, // timeout seconds
            2  // success threshold
        );

        // Check circuit breaker
        if ($circuitBreaker->isOpen()) {
            throw new \Rahasistiyak\LaravelAiIntegration\Exceptions\ProviderException(
                'Service temporarily unavailable due to circuit breaker'
            );
        }

        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $result = $callback();
                $circuitBreaker->recordSuccess();
                return $result;
            } catch (\Exception $e) {
                $lastException = $e;

                // Don't retry on certain errors
                if ($this->shouldNotRetry($e)) {
                    $circuitBreaker->recordFailure();
                    throw $e;
                }

                // Last attempt, record failure and throw
                if ($attempt === $maxRetries) {
                    $circuitBreaker->recordFailure();
                    throw $e;
                }

                // Exponential backoff: 100ms, 200ms, 400ms, etc.
                $delay = min(100 * pow(2, $attempt - 1), 1000);
                usleep($delay * 1000);
            }
        }

        throw $lastException;
    }

    /**
     * Determine if error should not be retried
     */
    protected function shouldNotRetry(\Exception $e): bool
    {
        // Don't retry validation errors (4xx)
        if ($e instanceof \Illuminate\Http\Client\RequestException) {
            $status = $e->response->status();
            return $status >= 400 && $status < 500 && $status !== 429;
        }

        return false;
    }

    /**
     * Get provider name for circuit breaker
     */
    protected function getProviderName(): string
    {
        return class_basename($this);
    }
}

