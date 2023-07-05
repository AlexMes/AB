<?php

namespace App\Facebook;

use App\Facebook\Commands\Cache;
use App\Facebook\Commands\CacheProfiles;
use App\Facebook\Commands\FlushComments;
use App\Facebook\Commands\Refresh;
use App\Facebook\Commands\StartCampaignsAdsets;
use App\Facebook\Http\Controllers\ConnectProfile;
use App\Facebook\Http\Controllers\DisconnectProfile;
use App\Facebook\Http\Controllers\ProcessCallback;
use App\Facebook\Http\FacebookClient;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FacebookServiceProvider extends ServiceProvider
{
    use Concerns\EventMap;

    /**
     * Register application services
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('facebook', FacebookApp::class);

        $this->app->bind('facebook-client', function ($app) {
            return new FacebookClient();
        });
        $this->app->bind('fb-default-client', function ($app) {
            return $app['facebook']::current()->facebook();
        });
        $this->app->bind('fb-default-marketing', function ($app) {
            return $app['facebook']::current()->api();
        });
    }

    /**
     * Bootstrap application services
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     *
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerCommands();
        $this->registerEvents();
    }

    /**
     * Register service routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::get('/facebook/connect', ConnectProfile::class)->name('facebook.connect');
        Route::get('/facebook/callback', ProcessCallback::class)->name('facebook.callback');
        Route::post('/facebook/disconnect', DisconnectProfile::class)->name('facebook.disconnect');
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
                Refresh::class,
                Cache::class,
                CacheProfiles::class,
                FlushComments::class,
                StartCampaignsAdsets::class,
            ]);
        }
    }
}
