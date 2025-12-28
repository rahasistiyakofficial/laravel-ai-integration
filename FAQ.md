# Frequently Asked Questions

## General

### What is Laravel AI Integration?
It's a comprehensive Laravel package that provides a unified API to interact with multiple AI providers (OpenAI, Anthropic, Google, etc.). It abstracts the differences between providers so you can switch between them easily.

### Which AI providers are supported?
Currently, we support:
- OpenAI (GPT-4, GPT-3.5)
- Anthropic (Claude 3 Opus, Sonnet, Haiku)
- Google (Gemini Pro)
- Groq (Llama 3, Mixtral)
- Ollama (Local models)

### What defines "v2.0.0 compatibility"?
Version 2.0.0 requires:
- PHP 8.2 or higher
- Laravel 11.x or 12.x
- Redis (optional, but recommended for caching)

---

## Usage & Implementation

### How do I switch providers dynamically?
You can use the `driver()` method:
```php
// Use default
AI::chat()->get();

// Use specific provider
AI::driver('anthropic')->chat(...);
```

### Can I change the model for a specific request?
Yes, use the `model()` method when using the fluent Chat API:
```php
AI::chat()
    ->model('gpt-4-turbo')
    ->messages(...)
    ->get();
```

*Note: The `model()` method is available on the Chat builder. If you are using the driver directly, pass the model in the parameters array.*

### How does response caching work?
Caching is enabled by default in v2.0. It generates a unique key based on the prompt, model, and parameters. If a matching key exists in the cache (Redis/DB), the cached response is returned instantly.
- **TTL**: configurable via `AI_CACHE_TTL` (default 1 hour)
- **Clear**: `php artisan ai:cache:clear`

---

## Troubleshooting

### I'm getting a "Driver not supported" error.
Ensure you have published the configuration file (`php artisan vendor:publish --tag=ai-config`) and that the driver is correctly defined in `config/ai.php`.

### Streaming isn't working/showing output.
Streaming requires the provider's support. Ensure you are using a provider that supports streaming (OpenAI, Anthropic, Groq). Also, ensure your server configuration (Nginx/Apache) allows SSE (Server-Sent Events) and doesn't buffer output.

### How do I debug API errors?
Enable debug mode in Laravel or check your logs. The package throws exceptions like `ProviderException` or `RateLimitException` which contain details from the API provider.

### My costs aren't tracking.
Ensure you have enabled tracking in your `.env`:
`AI_TRACKING_ENABLED=true`
And ran the migrations:
`php artisan migrate`

---

## Security & Privacy

### Where are API keys stored?
API keys are stored in your `.env` file and referenced in `config/ai.php`. They are never hardcoded or exposed by the package.

### Does the package log my data?
If `AI_STORE_REQUESTS` is true (default false), prompts and responses are stored in your database for analytics. You can disable this in config if strict privacy is required.

---

## Contributing

### How can I add a new provider?
1. Create a new Driver class extending `AbstractDriver`.
2. Implement `chat()`, `embed()`, etc.
3. Register it in `AiManager`.
4. Submit a PR!
