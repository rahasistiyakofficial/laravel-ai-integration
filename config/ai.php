<?php

return [
    'default' => env('AI_DEFAULT_PROVIDER', 'openai'),

    'providers' => [
        'openai' => [
            'driver' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'organization' => env('OPENAI_ORGANIZATION'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'timeout' => env('AI_TIMEOUT', 30),
            'models' => [
                'chat' => ['gpt-4', 'gpt-3.5-turbo'],
                'embedding' => ['text-embedding-ada-002'],
                'image' => ['dall-e-3', 'dall-e-2'],
            ],
        ],

        'ollama' => [
            'driver' => 'ollama',
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434'),
            'timeout' => env('OLLAMA_TIMEOUT', 120),
            'models' => [
                'chat' => ['llama3', 'mistral', 'codellama'],
                'embedding' => ['nomic-embed-text'],
            ],
        ],
    ],

    'cache' => [
        'enabled' => env('AI_CACHE_ENABLED', true),
        'driver' => env('AI_CACHE_DRIVER', 'redis'),
        'ttl' => env('AI_CACHE_TTL', 3600), // seconds
        'prefix' => 'ai',
    ],

    'fallbacks' => [
        'openai' => ['anthropic', 'ollama'],
        'anthropic' => ['openai', 'groq'],
    ],

    'features' => [
        'caching' => [
            'enabled' => env('AI_CACHE_ENABLED', true),
            'ttl' => env('AI_CACHE_TTL', 3600),
            'store' => env('AI_CACHE_STORE', 'redis'),
        ],

        'rate_limiting' => [
            'enabled' => env('AI_RATE_LIMIT_ENABLED', true),
            'requests_per_minute' => env('AI_RATE_LIMIT', 60),
            'strategy' => 'token_bucket',
        ],

        'tracking' => [
            'enabled' => env('AI_TRACKING_ENABLED', true),
            'store_requests' => env('AI_STORE_REQUESTS', true),
            'track_costs' => env('AI_TRACK_COSTS', true),
        ],

        'vector_store' => [
            'driver' => env('VECTOR_STORE_DRIVER', 'pinecone'),
            'connection' => env('VECTOR_STORE_CONNECTION'),
        ],
    ],

    'tasks' => [
        'classification' => [
            'default_model' => 'gpt-3.5-turbo',
            'cache_results' => true,
        ],

        'extraction' => [
            'default_model' => 'gpt-4',
            'structured_output' => true,
        ],
    ],
];
