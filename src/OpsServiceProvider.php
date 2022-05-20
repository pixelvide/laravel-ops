<?php

namespace Pixelvide\Ops;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class OpsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCommands();

        if (!config('ops.enabled')) {
            return;
        }

        Route::middlewareGroup('ops', config('ops.middleware', []));

        $this->registerRoutes();
        $this->registerPublishing();

        $this->loadViewsFrom(
            __DIR__.'/../resources/views', 'ops'
        );
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        });
    }

    /*
     * Get the Ops route group configuration array.
     *
     * @return array
     */
    private function routeConfiguration()
    {
        return [
            'domain'     => config('ops.domain', null),
            'namespace'  => 'Pixelvide\Ops\Http\Controllers',
            'prefix'     => config('ops.path'),
            'middleware' => 'ops',
        ];
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ops.php' => config_path('ops.php'),
            ], 'ops-config');
        }
    }

    /*
     * Register the package's commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\PublishCommand::class,
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/ops.php', 'ops'
        );
    }
}