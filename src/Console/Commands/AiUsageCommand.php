<?php

namespace Rahasistiyak\LaravelAiIntegration\Console\Commands;

use Illuminate\Console\Command;
use Rahasistiyak\LaravelAiIntegration\Models\AiRequest;

class AiUsageCommand extends Command
{
    protected $signature = 'ai:usage 
                            {--provider= : Filter by provider}
                            {--days=7 : Number of days to show}';

    protected $description = 'Show AI usage statistics and costs';

    public function handle()
    {
        $days = (int) $this->option('days');
        $provider = $this->option('provider');

        $query = AiRequest::query()
            ->where('created_at', '>=', now()->subDays($days));

        if ($provider) {
            $query->where('provider', $provider);
        }

        $requests = $query->get();

        if ($requests->isEmpty()) {
            $this->info('No AI requests found in the specified period.');
            return 0;
        }

        $this->displayOverview($requests, $days);
        $this->displayByProvider($requests);
        $this->displayCacheStats($requests);

        return 0;
    }

    protected function displayOverview($requests, $days)
    {
        $this->info("\n📊 AI Usage Overview (Last {$days} days)\n");

        $totalRequests = $requests->count();
        $totalCost = $requests->sum('cost');
        $totalTokens = $requests->sum('total_tokens');
        $avgDuration = $requests->avg('duration_ms');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Requests', number_format($totalRequests)],
                ['Total Cost', '$' . number_format($totalCost, 4)],
                ['Total Tokens', number_format($totalTokens)],
                ['Avg Duration', round($avgDuration) . ' ms'],
            ]
        );
    }

    protected function displayByProvider($requests)
    {
        $this->info("\n📦 Usage by Provider\n");

        $byProvider = $requests->groupBy('provider')->map(function ($group) {
            return [
                'requests' => $group->count(),
                'cost' => $group->sum('cost'),
                'tokens' => $group->sum('total_tokens'),
            ];
        });

        $rows = [];
        foreach ($byProvider as $provider => $stats) {
            $rows[] = [
                $provider,
                number_format($stats['requests']),
                '$' . number_format($stats['cost'], 4),
                number_format($stats['tokens']),
            ];
        }

        $this->table(
            ['Provider', 'Requests', 'Cost', 'Tokens'],
            $rows
        );
    }

    protected function displayCacheStats($requests)
    {
        $this->info("\n💾 Cache Performance\n");

        $cached = $requests->where('cached', true)->count();
        $total = $requests->count();
        $hitRate = $total > 0 ? round(($cached / $total) * 100, 2) : 0;
        $savedCost = $requests->where('cached', true)->sum('cost');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Cache Hit Rate', $hitRate . '%'],
                ['Cached Requests', number_format($cached) . ' / ' . number_format($total)],
                ['Estimated Savings', '$' . number_format($savedCost, 4)],
            ]
        );
    }
}
