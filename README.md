# Laravel AI Integration

> **Enterprise-ready Laravel package for seamless AI integration with multiple providers through a unified API.**

[![Latest Version](https://img.shields.io/packagist/v/rahasistiyak/laravel-ai-integration.svg?style=flat-square)](https://packagist.org/packages/rahasistiyak/laravel-ai-integration)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/rahasistiyakofficial/laravel-ai-integration/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/rahasistiyakofficial/laravel-ai-integration/actions)
[![PHP Version](https://img.shields.io/packagist/php-v/rahasistiyak/laravel-ai-integration.svg?style=flat-square)](https://packagist.org/packages/rahasistiyak/laravel-ai-integration)
[![License](https://img.shields.io/github/license/rahasistiyakofficial/laravel-ai-integration.svg?style=flat-square)](LICENSE)

**Laravel AI Integration** provides a unified, elegant API to interact with multiple AI providers including OpenAI, Anthropic (Claude), Google (Gemini), Ollama, and Groq. Built specifically for Laravel 11+, it abstracts provider complexity while offering powerful features like streaming, function calling, embeddings, and more.

---

## ✨ Features

- 🎯 **Multi-Provider Support**: OpenAI, Anthropic (Claude), Google (Gemini), Ollama, Groq
- 💬 **Chat Completions**: Standard and real-time streaming responses
- 🧠 **Vector Embeddings**: Generate embeddings for semantic search
- 🖼️ **Image Generation**: DALL-E and compatible APIs
- 🛠️ **Function Calling**: Tool/function use support
- 🔄 **Real-Time Streaming**: SSE streaming for chat responses
- 🎨 **Eloquent Integration**: AI-powered model traits
- ⚡ **Task Abstraction**: Pre-built tasks (classification, etc.)
- 💻 **Artisan Commands**: CLI tools for code generation
- 📦 **Queue Support**: Background job processing

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
