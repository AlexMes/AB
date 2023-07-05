<?php

namespace App\Services\Forge;

use Illuminate\Support\ServiceProvider;

class ForgeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->when(ForgeClient::class)
            ->needs('$token')
            ->give(static fn () => config('services.forge.token'));
    }

    public function register()
    {
        $this->app->singleton('forge', function ($app) {
            return new ForgeClient($app['config']['services.forge.token']);
        });
    }
}
