<?php

namespace Rahasistiyak\LaravelAiIntegration\Console\Commands;

use Illuminate\Console\Command;
use Rahasistiyak\LaravelAiIntegration\Facades\AI;

class AiGenerateCodeCommand extends Command
{
    protected $signature = 'ai:generate-code {prompt : The coding task to perform} {--language=php : The target language}';

    protected $description = 'Generate code using AI';

    public function handle()
    {
        $prompt = $this->argument('prompt');
        $language = $this->option('language');

        $this->info("Generating {$language} code...");

        // Using the Chat API for code generation for now
        $response = AI::chat()->messages([
            ['role' => 'system', 'content' => "You are an expert {$language} programmer. Output only code, no markdown backticks."],
            ['role' => 'user', 'content' => $prompt]
        ])->get();

        $this->line($response->content());

        return 0;
    }
}
