<?php

namespace Rahasistiyak\LaravelAiIntegration\Support;

class TokenCounter
{
    /**
     * Estimate token count for text
     * Note: This is a rough estimation. For accurate counting, integrate tiktoken library
     */
    public static function estimate(string $text): int
    {
        // Rough estimation: ~4 characters per token
        return (int) ceil(strlen($text) / 4);
    }

    /**
     * Count tokens in messages array
     */
    public static function countMessages(array $messages): int
    {
        $total = 0;

        foreach ($messages as $message) {
            if (isset($message['content'])) {
                $total += self::estimate($message['content']);
            }

            // Add overhead for message formatting
            $total += 4; // role, message overhead
        }

        return $total;
    }

    /**
     * Calculate total tokens for request
     */
    public static function calculateRequestTokens(array $messages, ?string $response = null): array
    {
        $inputTokens = self::countMessages($messages);
        $outputTokens = $response ? self::estimate($response) : 0;

        return [
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'total_tokens' => $inputTokens + $outputTokens,
        ];
    }
}
