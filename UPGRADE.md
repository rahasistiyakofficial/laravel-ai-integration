# Upgrade Guide from v1.x to v2.0.0

## Overview

Version 2.0.0 introduces significant performance improvements and new features:
- Response caching system
- Cost tracking and analytics
- Retry logic with circuit breaker
- Prompt template system

## Breaking Changes

### Configuration Structure
The `config/ai.php` file has been updated with new sections.

### Database Schema
New `ai_requests` table added for tracking.

## Step-by-Step Upgrade

### 1. Update Package

```bash
composer update rahasistiyak/laravel-ai-integration
```

### 2. Publish Updated Configuration

```bash
php artisan vendor:publish --tag=ai-config --force
```

**Review your existing configuration** and merge custom settings.

### 3. Add Environment Variables

Add these to your `.env` file:

```env
# Caching Configuration
AI_CACHE_ENABLED=true
AI_CACHE_DRIVER=redis
AI_CACHE_TTL=3600

# Tracking Configuration  
AI_TRACKING_ENABLED=true
AI_TRACK_COSTS=true
```

### 4. Run Database Migrations

```bash
php artisan migrate
```

This creates the `ai_requests` table for usage tracking.

### 5. Clear Application Cache

```bash
php artisan cache:clear
php artisan config:clear
```

### 6. Test Your Integration

Run your existing tests to ensure compatibility:

```bash
php artisan test
```

## New Features You Can Use

### Response Caching

Caching is **enabled by default**. No code changes needed!

To disable caching for specific requests:

```php
config(['ai.cache.enabled' => false]);
$response = AI::chat()->messages([...])->get();
```

### View Usage Statistics

```bash
php artisan ai:usage
php artisan ai:usage --provider=openai --days=30
```

### Clear AI Caches

```bash
php artisan ai:cache:clear
```

### Use Prompt Templates

```php
use Rahasistiyak\LaravelAiIntegration\Support\PromptTemplate;

$prompt = PromptTemplate::make('Classify: {{text}}')
    ->with(['text' => 'Your input here'])
    ->toMessages();

$response = AI::chat()->messages($prompt)->get();
```

## Configuration Changes

### Before (v1.x)
```php
'providers' => [...],
'fallbacks' => [...],
'features' => [...],
```

### After (v2.0)
```php
'providers' => [...],

'cache' => [
    'enabled' => true,
    'driver' => 'redis',
    'ttl' => 3600,
],

'fallbacks' => [...],
'features' => [
    // ... existing features
    'tracking' => [
        'enabled' => true,
        'track_costs' => true,
    ],
],
```

## Performance Improvements

- **Cache Hit Rate**: Typically 60-80% for repeated queries
- **Cost Savings**: Up to 60-80% reduction in API costs
- **Response Time**: 10-50ms for cached responses vs 500-2000ms for API calls

## Troubleshooting

### Issue: Tests Failing

**Solution**: Ensure test database migrations are run:
```bash
php artisan migrate --env=testing
```

### Issue: Cache Not Working

**Solution**: Verify Redis is running:
```bash
redis-cli ping
```

### Issue: Missing Configuration

**Solution**: Re-publish configuration:
```bash
php artisan vendor:publish --tag=ai-config --force
```

## Rollback

If you need to rollback to v1.x:

```bash
composer require rahasistiyak/laravel-ai-integration:^1.2
php artisan migrate:rollback
```

## Support

For issues or questions:
- GitHub Issues: https://github.com/rahasistiyakofficial/laravel-ai-integration/issues
- Documentation: See README.md

## What's Next?

Check the [ROADMAP.md](ROADMAP.md) for upcoming features in v2.5.0 and beyond!
