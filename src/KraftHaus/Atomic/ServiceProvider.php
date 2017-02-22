<?php

namespace KraftHaus\Atomic;

/*
 * This file is part of the Atomic package.
 *
 * (c) KraftHaus <hello@krafthaus.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/atomic.php' => config_path('atomic.php')
        ], 'config');

        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/atomic')
        ], 'views');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'atomic');
    }

    /**
     * Regsiter the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/atomic.php', 'atomic');

        $this->registerFactory();

        $this->registerRegistrar();

        $this->registerWidgets();
    }

    /**
     * Register the factory.
     */
    protected function registerFactory()
    {
        $this->app->singleton('atomic.factory', Factory::class);
    }

    /**
     * Register the registrar.
     */
    protected function registerRegistrar()
    {
        $this->app->singleton('atomic.registrar', Registrar::class);
    }

    /**
     * Register the widgets.
     */
    protected function registerWidgets()
    {
        $this->app['atomic.registrar']->register(config('atomic.components'));
    }
}