<?php

namespace Rahasistiyak\LaravelAiIntegration\Facades;

use Illuminate\Support\Facades\Facade;

class AI extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ai.manager';
    }

    public static function chat()
    {
        return app('ai.chat');
    }

    public static function embed()
    {
        return app('ai.embed');
    }

    public static function task()
    {
        return app('ai.task');
    }

    public static function image()
    {
        return app('ai.image');
    }
}
