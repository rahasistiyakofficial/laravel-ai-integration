# Changelog

All notable changes to `laravel-ai-integration` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2024-12-28

### Added

#### Performance & Caching
- **Response Caching System**
  - `CacheService` for intelligent response caching
  - Redis and database driver support
  - Content-based cache key generation
  - Cache hit/miss tracking and statistics
  - `ai:cache:clear` command to flush caches

#### Cost Tracking & Analytics
- **Token Counter & Cost Calculator**
  - `TokenCounter` for estimating token usage
  - `CostCalculator` with pricing for all 5 providers
  - `AiRequest` model for tracking all requests
  - Database migration for usage analytics
  - `ai:usage` command with detailed statistics
  - Cost tracking per provider and model

#### Reliability & Resilience
- **Retry Logic & Circuit Breaker**
  - Exponential backoff retry mechanism
  - Circuit breaker pattern for provider failures
  - Automatic failover handling
  - Configurable retry thresholds

#### Developer Experience
- **Prompt Template System**
  - `PromptTemplate` class for reusable prompts
  - Variable substitution support
  - Template library in `resources/prompts`
  - Pre-built templates for common tasks

### Changed
- **ChatService** now includes:
  - Automatic caching of responses
  - Request duration tracking
  - Cost calculation and logging
- **AbstractDriver** enhanced with:
  - Retry logic wrapper
  - Circuit breaker integration
  - Better error handling

### Breaking Changes
- **Configuration Structure**: Added `cache` section to `config/ai.php`
- **Tracking Feature**: Added `tracking` configuration in `features`
- **Requirements**: Redis recommended for optimal caching performance

### Migration Guide

#### Step 1: Publish New Configuration
```bash
php artisan vendor:publish --tag=ai-config --force
```

#### Step 2: Update Environment Variables
```env
# Add to .env
AI_CACHE_ENABLED=true
AI_CACHE_DRIVER=redis
AI_CACHE_TTL=3600

AI_TRACKING_ENABLED=true
```

#### Step 3: Run Migrations
```bash
php artisan migrate
```

#### Step 4: Clear Old Caches (Optional)
```bash
php artisan ai:cache:clear
```

### Performance Improvements
- Up to 80% reduction in API calls through caching
- Faster response times with cache hits
- Reduced costs through intelligent caching

### New Commands
- `ai:cache:clear` - Clear all AI response caches
- `ai:usage` - View usage statistics and costs

---

## [1.2.0] - 2024-12-28

### Changed
- Updated `composer.json` version to 1.2.0
- Updated author email

---

## [1.1.0] - 2024-12-28

### Added
- GitHub Actions workflow for automated testing

---

## [1.0.0] - 2024-12-28

### Added
- Initial release
- Support for 5 AI providers (OpenAI, Anthropic, Google, Ollama, Groq)
- Chat completion with streaming
- Embeddings generation
- Task system with classification
- Eloquent integration
- Console commands
- Queue support
- Comprehensive test suite

[2.0.0]: https://github.com/rahasistiyakofficial/laravel-ai-integration/compare/v1.2.0...v2.0.0
[1.2.0]: https://github.com/rahasistiyakofficial/laravel-ai-integration/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/rahasistiyakofficial/laravel-ai-integration/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/rahasistiyakofficial/laravel-ai-integration/releases/tag/v1.0.0
