<?php

namespace App\Deluge;

use App\Deluge\Concenrs\EventMap;
use App\Deluge\Console\CheckDomains;
use App\Deluge\Policies\DomainPolicy;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DelugeServiceProvider extends ServiceProvider
{
    use EventMap;

    /**
     * Bootstrap application services
     *
     * @return void
     */
    public function boot()
    {
        $this->registerEvents();
        $this->registerRoutes();
        $this->registerPolicies();
        $this->registerResources();
        $this->registerPublishableFiles();
    }

    /**
     * Register module routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'domain'     => config('deluge.domain'),
            'as'         => 'deluge.',
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        });
    }

    /**
     * Register module resources
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'deluge');
    }

    /**
     * Bindings to publishable files such
     * as configs, assets, etc.
     *
     * @return void
     */
    protected function registerPublishableFiles()
    {
        $this->publishes([
            __DIR__ . '/config/deluge.php' => config_path('deluge.php')
        ]);
    }

    /**
     * Bind policies for module
     *
     * @return void
     */
    protected function registerPolicies()
    {
        Gate::define('exportPeformanceReport', fn (User $user) => $user->hasRole([User::ADMIN,User::HEAD,User::TEAMLEAD]));
        Gate::define('exportQuizReport', fn (User $user) => $user->hasRole([User::ADMIN,User::HEAD,User::TEAMLEAD]));
        Gate::policy(Domain::class, DomainPolicy::class);
    }

    public function register()
    {
        $this->commands([
            CheckDomains::class,
        ]);
    }
}
