<?php

namespace App\VK;

use App\VK\Commands\Refresh;
use App\VK\Concerns\EventMap;
use App\VK\Http\Controllers\ConnectProfile;
use App\VK\Http\Controllers\ProcessCallback;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class VKServiceProvider extends ServiceProvider
{
    use EventMap;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('vk', VKApp::class);
        $this->app->bind(VKApp::class, function ($app, $params) {
            return new VKApp(
                config('vk.app_id'),
                config('vk.app_secret'),
                $params['accessToken'] ?? config('vk.default_access_token'),
                $params['apiVersion'] ?? config('vk.default_api_version')
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
        $this->registerEvents();
    }

    /**
     * @return void
     */
    protected function registerRoutes()
    {
        Route::middleware(['web'])->group(function () {
            Route::get('/vk/connect', ConnectProfile::class)->name('vk.connect');
            Route::get('/vk/callback', ProcessCallback::class)->name('vk.callback');
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
                Refresh::class,
            ]);
        }
    }
}
