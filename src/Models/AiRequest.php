<?php

namespace Rahasistiyak\LaravelAiIntegration\Models;

use Illuminate\Database\Eloquent\Model;

class AiRequest extends Model
{
    protected $fillable = [
        'provider',
        'model',
        'messages',
        'response',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'cost',
        'duration_ms',
        'cached',
    ];

    protected $casts = [
        'messages' => 'array',
        'cached' => 'boolean',
        'cost' => 'decimal:6',
    ];

    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeCached($query)
    {
        return $query->where('cached', true);
    }

    public function scopeNotCached($query)
    {
        return $query->where('cached', false);
    }
}
