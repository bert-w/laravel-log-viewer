<?php

namespace BertW\LaravelLogViewer;

use BertW\LaravelLogViewer\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LogViewerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'logviewer');

        $this->publishes([
            __DIR__ . '/../config/logviewer.php' => config_path('logviewer.php'),
        ], 'config');
    }

    public function register()
    {
        $this->app->booted(function ($app) {
            Route::namespace('BertW\LaravelLogViewer\Http\Controllers')
                ->middleware([Authenticate::class])
                ->prefix($app['config']->get('logviewer.url') ?? '/logviewer')
                ->group(__DIR__ . '/Http/routes.php');
        });

        $this->app->singleton(LogViewer::class, function ($app) {
            return new LogViewer($app);
        });
    }
}
