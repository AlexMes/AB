<?php

namespace App\Integrations;

use App\Integrations\Commands\SenToForms;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class IntegrationServiceProvider extends ServiceProvider
{
    use Concerns\AccessPolicy;

    /**
     * Bootstrap application services
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerApiRoutes();
        $this->registerPostbaksRoutes();
        $this->registerCommands();
    }

    /**
     * Register router mapping for the integration module
     *
     * @return void
     */
    protected function registerApiRoutes()
    {
        Route::group([
            'prefix'     => 'api/integrations',
            'namespace'  => 'App\Integrations\Http\Controllers',
            'middleware' => ['api','auth:api','firewall'],
            'as'         => 'api.integration.'
        ], function () {
            $this->loadRoutesFrom(sprintf("%s/Http/api.php", __DIR__));
        });
    }


    /**
     * Register router mapping for the postbaks integration module
     *
     * @return void
     */
    protected function registerPostbaksRoutes()
    {
        Route::group([
            'prefix'     => 'postback',
            'namespace'  => 'App\Integrations\Http\Postbacks',
            'middleware' => ['web'],
            'as'         => 'postback.'
        ], function () {
            $this->loadRoutesFrom(sprintf("%s/Http/postbacks.php", __DIR__));
        });
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
                SenToForms::class,
            ]);
        }
    }
}
