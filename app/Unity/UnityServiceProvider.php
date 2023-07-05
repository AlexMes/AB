<?php

namespace App\Unity;

use App\Unity\Commands\Cache;
use App\Unity\Commands\Refresh;
use Illuminate\Support\ServiceProvider;

class UnityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('unity', UnityApp::class);
        $this->app->bind(UnityApp::class, function ($app, $params) {
            return new UnityApp(
                $params['apiKey'] ?? null,
                $params['keyId'] ?? null,
                $params['secretKey'] ?? null,
                $params['apiVersion'] ?? null
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerCommands();
    }

    /**
     * @return void
     */
    protected function registerRoutes()
    {
        //
    }

    /**
     * Register module commands
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Cache::class,
                Refresh::class,
            ]);
        }
    }
}
