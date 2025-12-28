<?php

namespace Rahasistiyak\LaravelAiIntegration\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class ChatResponse implements Arrayable, Jsonable
{
    protected array $raw;

    public function __construct(array $raw)
    {
        $this->raw = $raw;
    }

    public function content(): ?string
    {
        return $this->raw['choices'][0]['message']['content'] ?? null;
    }

    public function toArray(): array
    {
        return $this->raw;
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->raw, $options);
    }
}
