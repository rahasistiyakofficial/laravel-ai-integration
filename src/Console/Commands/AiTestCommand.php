<?php

namespace Rahasistiyak\LaravelAiIntegration\Console\Commands;

use Illuminate\Console\Command;
use Rahasistiyak\LaravelAiIntegration\Facades\AI;

class AiTestCommand extends Command
{
    protected $signature = 'ai:test {provider? : The provider to test (openai, anthropic, etc.)}';
    protected $description = 'Test connection to AI providers';

    public function handle()
    {
        $provider = $this->argument('provider') ?: config('ai.default');
        $this->info("Testing connection to [{$provider}]...");

        try {
            $start = microtime(true);

            $response = AI::driver($provider)->chat([
                ['role' => 'user', 'content' => 'Hello, reply with "OK".']
            ], ['max_tokens' => 5]);

            $duration = round((microtime(true) - $start) * 1000);

            $this->info("✅ Connection successful!");
            $this->line("Response: " . $response->content());
            $this->line("Duration: {$duration}ms");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Connection failed!");
            $this->error($e->getMessage());

            return 1;
        }
    }
}
