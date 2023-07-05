<?php

namespace App\Services\IpApi;

use Illuminate\Support\ServiceProvider;

class IpApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services
     *
     * @return void
     */
    public function boot()
    {
        $this->app->when(IpApi::class)
            ->needs('$token')
            ->give(config('services.ipapi.token'));
    }
}
