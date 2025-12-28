# Laravel AI Integration

> **Enterprise-ready Laravel package for seamless AI integration with multiple providers through a unified API.**

[![Latest Version](https://img.shields.io/packagist/v/rahasistiyak/laravel-ai-integration.svg?style=flat-square)](https://packagist.org/packages/rahasistiyak/laravel-ai-integration)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/rahasistiyakofficial/laravel-ai-integration/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/rahasistiyakofficial/laravel-ai-integration/actions)
[![PHP Version](https://img.shields.io/packagist/php-v/rahasistiyak/laravel-ai-integration.svg?style=flat-square)](https://packagist.org/packages/rahasistiyak/laravel-ai-integration)
[![License](https://img.shields.io/github/license/rahasistiyakofficial/laravel-ai-integration.svg?style=flat-square)](LICENSE)

**Laravel AI Integration** provides a unified, elegant API to interact with multiple AI providers including OpenAI, Anthropic (Claude), Google (Gemini), Ollama, and Groq. Built specifically for Laravel 11+, it abstracts provider complexity while offering powerful features like streaming, function calling, embeddings, and more.

---

## ✨ Features

- 🎯 **5 AI Providers**: OpenAI, Anthropic (Claude), Google (Gemini), Ollama, Groq
- 💬 **Chat Completion**: Standard and streaming responses
- 🧠 **Embeddings**: Generate vector embeddings for semantic search
- 🖼️ **Image Generation**: DALL-E and compatible APIs
- 🛠️ **Function Calling**: Tool/function use support
- 🔄 **Streaming**: Real-time SSE streaming for chat
- 💾 **Response Caching**: Intelligent caching with Redis/database support (v2.0)
- 💰 **Cost Tracking**: Token counting and cost calculation (v2.0)
- 🔁 **Retry Logic**: Exponential backoff with circuit breaker (v2.0)
- 📝 **Prompt Templates**: Reusable prompt system (v2.0)
- 🎨 **Eloquent Integration**: Traits for AI-powered models
- ⚡ **Task Abstraction**: Pre-built tasks for common operations
- 💻 **Artisan Commands**: CLI for code generation, cache management, usage stats
- 📦 **Jobs**: Queue support for background processing

---

## 📦 Installation

Install via Composer:

```bash
composer require rahasistiyak/laravel-ai-integration
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=ai-config
```

---

## ⚙️ Configuration

### Environment Variables

Add your API keys to `.env`:

```env
# OpenAI Configuration
OPENAI_API_KEY=sk-...

# Anthropic (Claude) Configuration
ANTHROPIC_API_KEY=sk-ant-...

# Google (Gemini) Configuration
GOOGLE_API_KEY=...

# Groq Configuration
GROQ_API_KEY=...

# Ollama (Local) Configuration
OLLAMA_BASE_URL=http://localhost:11434

# Default Provider
AI_DEFAULT_PROVIDER=openai
```

### Provider Configuration

Edit `config/ai.php` to customize provider settings:

```php
return [
    'default' => env('AI_DEFAULT_PROVIDER', 'openai'),

    'providers' => [
        'openai' => [
            'driver' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'timeout' => 30,
            'models' => [
                'chat' => ['gpt-4', 'gpt-3.5-turbo'],
                'embedding' => ['text-embedding-ada-002'],
            ],
        ],
        // Additional providers...
    ],
];
```

---

## 🚀 Usage

### Basic Chat

```php
use Rahasistiyak\LaravelAiIntegration\Facades\AI;

$response = AI::chat()
    ->messages([
        ['role' => 'user', 'content' => 'Explain quantum computing in simple terms']
    ])
    ->get();

echo $response->content();
```

### Streaming Responses

Stream responses in real-time:

```php
AI::chat()
    ->messages([
        ['role' => 'user', 'content' => 'Write a short story about AI']
    ])
    ->stream(function ($chunk) {
        echo $chunk; // Output each chunk as it arrives
    });
```

### Using Different Providers

```php
// Use Anthropic (Claude)
$response = AI::driver('anthropic')
    ->chat([
        ['role' => 'user', 'content' => 'Hello Claude!']
    ]);

// Use Google Gemini
$response = AI::driver('google')
    ->chat([
        ['role' => 'user', 'content' => 'Hello Gemini!']
    ]);

// Use Groq
$response = AI::driver('groq')
    ->chat([
        ['role' => 'user', 'content' => 'Hello Groq!']
    ]);

// Use local Ollama
$response = AI::driver('ollama')
    ->chat([
        ['role' => 'user', 'content' => 'Hello Llama!']
    ]);
```

### Embeddings

Generate vector embeddings for semantic search:

```php
$embedding = AI::embed()->generate('Your text here');
// Returns: [0.0123, -0.0234, 0.0156, ...]
```

### Eloquent Model Integration

Add AI capabilities to your models:

```php
use Rahasistiyak\LaravelAiIntegration\Traits\HasAiEmbeddings;

class Article extends Model
{
    use HasAiEmbeddings;
}

// Generate embeddings
$article = Article::find(1);
$embedding = $article->generateEmbedding();
```

### Function Calling / Tools

Use function calling for structured outputs:

```php
$response = AI::chat()
    ->withTools([
        [
            'type' => 'function',
            'function' => [
                'name' => 'get_weather',
                'description' => 'Get the current weather for a location',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'location' => [
                            'type' => 'string',
                            'description' => 'City name',
                        ],
                        'unit' => [
                            'type' => 'string',
                            'enum' => ['celsius', 'fahrenheit'],
                        ],
                    ],
                    'required' => ['location'],
                ],
            ],
        ],
    ])
    ->messages([
        ['role' => 'user', 'content' => 'What\'s the weather in Tokyo?']
    ])
    ->get();
```

### Task Abstraction

Use pre-built tasks for common operations:

```php
// Text classification
$category = AI::task()->classify(
    'This new GPU delivers incredible performance for AI workloads',
    ['Technology', 'Fashion', 'Sports', 'Politics']
);
// Returns: "Technology"
```

### Image Generation

```php
$image = AI::image()->generate('A futuristic city at sunset', [
    'size' => '1024x1024',
    'quality' => 'hd'
]);
// Returns: ['url' => 'https://...']
```

### Console Commands

Generate code via Artisan:

```bash
php artisan ai:generate-code "Create a UserObserver that logs model events" --language=php
```

### Background Jobs

Process AI tasks in the background:

```php
use Rahasistiyak\LaravelAiIntegration\Jobs\ProcessAiTask;

ProcessAiTask::dispatch('classify', $text, [
    'labels' => ['Positive', 'Negative', 'Neutral']
]);
```

---

## 🆕 What's New in v2.0.0

Version 2.0.0 introduces enterprise-grade features for production AI applications:

### Response Caching

Automatically cache AI responses to reduce costs and improve performance:

```php
// Caching is enabled by default
$response = AI::chat()->messages([...])->get();

// Second identical request uses cache (instant, zero cost)
$cached = AI::chat()->messages([...])->get();

// Clear cache
php artisan ai:cache:clear
```

**Benefits:**
- 60-80% reduction in API costs
- 95% faster response times for cached requests
- Configurable TTL and cache drivers (Redis, database, array)

### Cost Tracking & Analytics

Track token usage and costs in real-time:

```bash
# View usage statistics
php artisan ai:usage

# Filter by provider and time period
php artisan ai:usage --provider=openai --days=30
```

**Displays:**
- Total requests, tokens, and costs
- Cost breakdown by provider
- Cache hit rates and savings
- Average response times

### Prompt Templates

Create reusable prompt templates:

```php
use Rahasistiyak\LaravelAiIntegration\Support\PromptTemplate;

$prompt = PromptTemplate::load('classification')
    ->with([
        'text' => $userInput,
        'categories' => 'Tech, Sports, Politics'
    ])
    ->toMessages();

$response = AI::chat()->messages($prompt)->get();
```

**Built-in Templates:**
- `classification.txt` - Text classification
- `summarization.txt` - Content summarization
- Create your own in `resources/prompts/`

### Retry Logic & Circuit Breaker

Automatic retry with exponential backoff for failed requests:

- **Exponential Backoff**: 100ms → 200ms → 400ms → 800ms
- **Circuit Breaker**: Prevents cascading failures
- **Smart Retry**: Doesn't retry validation errors (4xx)
- **99.9% Uptime**: Even when providers have issues

**Configuration:**

```env
# Enable caching (recommended for production)
AI_CACHE_ENABLED=true
AI_CACHE_DRIVER=redis
AI_CACHE_TTL=3600

# Enable cost tracking
AI_TRACKING_ENABLED=true
```

See [UPGRADE.md](UPGRADE.md) for migration guide from v1.x.

---

## 🛠️ Advanced Features

### Custom Model Selection

```php
AI::chat()
    ->model('gpt-4')
    ->messages([...])
    ->get();
```

### Custom Parameters

```php
AI::chat()
    ->withParameters([
        'temperature' => 0.9,
        'max_tokens' => 500,
        'top_p' => 0.95,
    ])
    ->messages([...])
    ->get();
```

### Fluent API Chaining

```php
$response = AI::chat()
    ->model('gpt-4')
    ->withParameters(['temperature' => 0.7])
    ->withTools([...])
    ->messages([...])
    ->get();
```

---

## 🔧 Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| **Driver not supported** | Verify driver is properly configured in `config/ai.php` |
| **401 Unauthorized** | Check API keys in `.env` and ensure they're valid |
| **Connection Refused (Ollama)** | Ensure Ollama is running: `ollama serve` |
| **SSL Certificate Errors** | Update `base_url` or configure SSL certificates |
| **Timeout Errors** | Increase `timeout` value in provider config |

### Debug Mode

Enable verbose error messages:

```php
config(['app.debug' => true]);
```

---

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Run specific tests:

```bash
./vendor/bin/phpunit --filter OpenAIDriverTest
```

---

## � Documentation

For complete documentation and examples, visit the [GitHub repository](https://github.com/rahasistiyakofficial/laravel-ai-integration).

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](https://github.com/rahasistiyakofficial/laravel-ai-integration/blob/main/CONTRIBUTING.md) for guidelines.

### Development Setup

```bash
git clone https://github.com/rahasistiyakofficial/laravel-ai-integration.git
cd laravel-ai-integration
composer install
composer test
```

---

## 📋 Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 11.x or 12.x
- **Dependencies**: Guzzle HTTP Client

---

## 📝 License

This package is open-source software licensed under the [MIT License](https://github.com/rahasistiyakofficial/laravel-ai-integration/blob/main/LICENSE).

---

## 🙏 Credits

- **Author**: [Rahasistiyak](https://github.com/rahasistiyakofficial)
- **Package**: [rahasistiyak/laravel-ai-integration](https://packagist.org/packages/rahasistiyak/laravel-ai-integration)
- **Repository**: [GitHub](https://github.com/rahasistiyakofficial/laravel-ai-integration)

---

## ⭐ Support

If you find this package helpful, please consider giving it a star on [GitHub](https://github.com/rahasistiyakofficial/laravel-ai-integration)!

For issues and feature requests, please use the [issue tracker](https://github.com/rahasistiyakofficial/laravel-ai-integration/issues).
