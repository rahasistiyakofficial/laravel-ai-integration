<?php

namespace Rahasistiyak\LaravelAiIntegration\Support;

use Illuminate\Support\Facades\Cache;

class CircuitBreaker
{
    protected string $service;
    protected int $failureThreshold;
    protected int $timeout;
    protected int $successThreshold;

    public function __construct(
        string $service,
        int $failureThreshold = 5,
        int $timeout = 60,
        int $successThreshold = 2
    ) {
        $this->service = $service;
        $this->failureThreshold = $failureThreshold;
        $this->timeout = $timeout;
        $this->successThreshold = $successThreshold;
    }

    /**
     * Check if circuit is open (service unavailable)
     */
    public function isOpen(): bool
    {
        $state = $this->getState();

        if ($state === 'open') {
            // Check if timeout has passed
            $openedAt = Cache::get($this->getKey('opened_at'));
            if ($openedAt && time() - $openedAt > $this->timeout) {
                $this->halfOpen();
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Record a successful call
     */
    public function recordSuccess(): void
    {
        $state = $this->getState();

        if ($state === 'half-open') {
            $successes = (int) Cache::get($this->getKey('successes'), 0);
            $successes++;

            if ($successes >= $this->successThreshold) {
                $this->close();
            } else {
                Cache::put($this->getKey('successes'), $successes, now()->addMinutes(5));
            }
        } elseif ($state === 'closed') {
            // Reset failure count on success
            Cache::forget($this->getKey('failures'));
        }
    }

    /**
     * Record a failed call
     */
    public function recordFailure(): void
    {
        $failures = (int) Cache::get($this->getKey('failures'), 0);
        $failures++;

        Cache::put($this->getKey('failures'), $failures, now()->addMinutes(5));

        if ($failures >= $this->failureThreshold) {
            $this->open();
        }
    }

    /**
     * Open the circuit (stop allowing requests)
     */
    protected function open(): void
    {
        Cache::put($this->getKey('state'), 'open', now()->addHours(1));
        Cache::put($this->getKey('opened_at'), time(), now()->addHours(1));
        Cache::forget($this->getKey('failures'));
    }

    /**
     * Half-open the circuit (allow limited requests to test)
     */
    protected function halfOpen(): void
    {
        Cache::put($this->getKey('state'), 'half-open', now()->addMinutes(5));
        Cache::forget($this->getKey('successes'));
    }

    /**
     * Close the circuit (normal operation)
     */
    protected function close(): void
    {
        Cache::put($this->getKey('state'), 'closed', now()->addHours(1));
        Cache::forget($this->getKey('successes'));
        Cache::forget($this->getKey('opened_at'));
    }

    /**
     * Get current circuit state
     */
    protected function getState(): string
    {
        return Cache::get($this->getKey('state'), 'closed');
    }

    /**
     * Generate cache key
     */
    protected function getKey(string $suffix): string
    {
        return "circuit_breaker:{$this->service}:{$suffix}";
    }

    /**
     * Get circuit status for monitoring
     */
    public function getStatus(): array
    {
        return [
            'service' => $this->service,
            'state' => $this->getState(),
            'failures' => (int) Cache::get($this->getKey('failures'), 0),
            'successes' => (int) Cache::get($this->getKey('successes'), 0),
        ];
    }
}
