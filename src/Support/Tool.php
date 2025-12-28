<?php

namespace Rahasistiyak\LaravelAiIntegration\Support;

class Tool
{
    public string $type = 'function';
    public array $function;

    public function __construct(string $name, string $description, array $parameters = [])
    {
        $this->function = [
            'name' => $name,
            'description' => $description,
            'parameters' => $parameters,
        ];
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'function' => $this->function,
        ];
    }
}
