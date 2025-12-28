<?php

namespace Rahasistiyak\LaravelAiIntegration\Tasks;

class ClassificationTask extends BaseTask
{
    public function execute($input, array $options = [])
    {
        $labels = $options['labels'] ?? [];
        $type = $options['type'] ?? 'general';

        $template = "Classify the following text into one of these categories: {{labels}}.\n\nText: {{input}}\n\nCategory:";

        $messages = $this->buildPrompt($template, [
            'labels' => implode(', ', $labels),
            'input' => $input,
        ]);

        $response = $this->ai->messages($messages)->get();

        return trim($response->content());
    }
}
