<?php

declare(strict_types=1);

namespace Wave\Plugins;

use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PluginManager::class, fn ($app) => new PluginManager($app));
    }

    public function boot(): void
    {
        $pluginManager = $this->app->make(PluginManager::class);
        $pluginManager->loadPlugins();
    }
}
