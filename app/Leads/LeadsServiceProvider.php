<?php

namespace App\Leads;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LeadsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap application services
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
    }

    /**
     * Register module routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix'     => 'leads',
            'as'         => 'leads.',
            'middleware' => 'api'
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        });
    }
}
