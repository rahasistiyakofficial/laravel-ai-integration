<?php

namespace Rahasistiyak\LaravelAiIntegration\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Rahasistiyak\LaravelAiIntegration\Facades\AI;

class ProcessAiTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $type;
    protected string $input;
    protected array $options;

    public function __construct(string $type, string $input, array $options = [])
    {
        $this->type = $type;
        $this->input = $input;
        $this->options = $options;
    }

    public function handle()
    {
        // Execute the task dynamically
        // Note: Ideally we'd store the result somewhere, but for this generic job example 
        // we'll mainly demonstrate dispatching capability.
        // In a real app, this might accept a Model $resultable to save output to.

        if ($this->type === 'classify') {
            AI::task()->classify($this->input, $this->options['labels'] ?? []);
        }
    }
}
