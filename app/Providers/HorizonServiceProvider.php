<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap application services
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        if (now('Europe/Kiev')->hour >= 19 || now('Europe/Kiev')->hour <= 6) {
            Horizon::night();
        }
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewHorizon', function ($user) {
            return $user->id === 1;
        });
    }
}
