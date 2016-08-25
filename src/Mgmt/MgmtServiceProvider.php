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
            require __DIR__ . '/../routes.php';
        }

        // Setup views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'mgmt');

        // Publish custom assets
        $this->publishes([
            // __DIR__ . '/../../resources/views' => resource_path('views/vendor/mgmt'),
            __DIR__ . '/../../public/css' => public_path('css'),
            __DIR__ . '/../../public/js' => public_path('js'),
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
            __DIR__ . '/../../database/seeds' => database_path('seeds')
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
        $loader->alias('FormGroup', 'Olorin\Support\FormGroup');
    }
}
