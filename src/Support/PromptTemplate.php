<?php

namespace Rahasistiyak\LaravelAiIntegration\Support;

class PromptTemplate
{
    protected string $template;
    protected array $variables = [];

    public function __construct(string $template)
    {
        $this->template = $template;
    }

    /**
     * Set template variables
     */
    public function with(array $variables): self
    {
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }

    /**
     * Render the template with variables
     */
    public function render(): string
    {
        $content = $this->template;

        foreach ($this->variables as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }

        return $content;
    }

    /**
     * Get as messages array for AI
     */
    public function toMessages(string $role = 'user'): array
    {
        return [
            ['role' => $role, 'content' => $this->render()]
        ];
    }

    /**
     * Load template from file
     */
    public static function load(string $name): self
    {
        $path = resource_path("prompts/{$name}.txt");

        if (!file_exists($path)) {
            throw new \RuntimeException("Prompt template '{$name}' not found");
        }

        return new self(file_get_contents($path));
    }

    /**
     * Create from string
     */
    public static function make(string $template): self
    {
        return new self($template);
    }
}
