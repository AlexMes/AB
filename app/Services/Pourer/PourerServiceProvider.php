<?php

namespace App\Services\Pourer;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PourerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerApiRoutes();
    }

    /**
     * Register router mapping for the integration module
     *
     * @return void
     */
    protected function registerApiRoutes()
    {
        Route::group([
            'prefix'     => 'api/pourer',
            'namespace'  => 'App\Services\Pourer\Http\Controllers',
            'middleware' => ['api','pourer'],
            'as'         => 'api.pourer.'
        ], function () {
            $this->loadRoutesFrom(sprintf("%s/Http/api.php", __DIR__));
        });
    }
}
