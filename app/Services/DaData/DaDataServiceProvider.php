<?php

namespace App\Services\DaData;

use Illuminate\Support\ServiceProvider;

class DaDataServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            'dadata',
            fn ($app) => new DaData(config('services.dadata.token'), config('services.dadata.secret'))
        );
    }
}
