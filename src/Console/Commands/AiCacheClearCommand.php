<?php

namespace Rahasistiyak\LaravelAiIntegration\Console\Commands;

use Illuminate\Console\Command;
use Rahasistiyak\LaravelAiIntegration\Services\CacheService;

class AiCacheClearCommand extends Command
{
    protected $signature = 'ai:cache:clear';
    protected $description = 'Clear all AI response caches';

    public function handle(CacheService $cacheService)
    {
        $this->info('Clearing AI caches...');

        $cacheService->flush();

        $this->info('✓ All AI caches cleared successfully!');

        return 0;
    }
}
