<?php

namespace Rahasistiyak\LaravelAiIntegration\Support;

class CostCalculator
{
    /**
     * Pricing per 1M tokens (input/output) for each provider/model
     */
    protected static array $pricing = [
        'openai' => [
            'gpt-4' => ['input' => 30.00, 'output' => 60.00],
            'gpt-4-turbo' => ['input' => 10.00, 'output' => 30.00],
            'gpt-3.5-turbo' => ['input' => 0.50, 'output' => 1.50],
        ],
        'anthropic' => [
            'claude-3-opus-20240229' => ['input' => 15.00, 'output' => 75.00],
            'claude-3-sonnet-20240229' => ['input' => 3.00, 'output' => 15.00],
            'claude-3-haiku-20240307' => ['input' => 0.25, 'output' => 1.25],
        ],
        'google' => [
            'gemini-pro' => ['input' => 0.50, 'output' => 1.50],
        ],
        'groq' => [
            'mixtral-8x7b-32768' => ['input' => 0.27, 'output' => 0.27],
            'llama3-70b-8192' => ['input' => 0.59, 'output' => 0.79],
        ],
    ];

    /**
     * Calculate cost for a request
     */
    public static function calculate(
        string $provider,
        string $model,
        int $inputTokens,
        int $outputTokens
    ): float {
        $pricing = self::getPricing($provider, $model);

        if (!$pricing) {
            return 0.0;
        }

        $inputCost = ($inputTokens / 1_000_000) * $pricing['input'];
        $outputCost = ($outputTokens / 1_000_000) * $pricing['output'];

        return round($inputCost + $outputCost, 6);
    }

    /**
     * Get pricing for provider/model
     */
    protected static function getPricing(string $provider, string $model): ?array
    {
        return self::$pricing[$provider][$model] ?? null;
    }

    /**
     * Get all pricing information
     */
    public static function getAllPricing(): array
    {
        return self::$pricing;
    }

    /**
     * Add or update pricing for a model
     */
    public static function setPricing(string $provider, string $model, float $inputPrice, float $outputPrice): void
    {
        if (!isset(self::$pricing[$provider])) {
            self::$pricing[$provider] = [];
        }

        self::$pricing[$provider][$model] = [
            'input' => $inputPrice,
            'output' => $outputPrice,
        ];
    }
}
