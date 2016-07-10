<?php

namespace Olorin\Mgmt;

use Illuminate\Support\ServiceProvider;

class MgmtServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Setup routes
        if (!$this->app->routesAreCached()) {
            require __DIR__ . '/routes.php';
        }

        // Setup views
        $this->loadViewsFrom(__DIR__ . '/views', 'MGMT');

        // Publish customizable assets
        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/mgmt'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register dependency ServiceProviders
        $this->app->register('Collective\Html\HtmlServiceProvider');

        // Load dependency Facades
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Form', 'Collective\Html\FormFacade');
        $loader->alias('Html', 'Collective\Html\HtmlFacade');
    }
}
