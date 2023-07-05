<?php

namespace App\Services\GenderApi;

use Illuminate\Support\ServiceProvider;

class GenderApiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->when(GenderAPI::class)
            ->needs('$token')
            ->give(config('services.genderapi.token'));

        $this->app->bind(GenderAPI::class, GenderAPI::class);
    }
}
