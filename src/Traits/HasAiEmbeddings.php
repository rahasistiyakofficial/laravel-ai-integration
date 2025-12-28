<?php

namespace Rahasistiyak\LaravelAiIntegration\Traits;

use Rahasistiyak\LaravelAiIntegration\Facades\AI;

trait HasAiEmbeddings
{
    public function generateEmbedding(): array
    {
        $text = $this->toEmbeddingString();
        return AI::embed()->generate($text);
    }

    public function toEmbeddingString(): string
    {
        return $this->toJson();
    }
}
