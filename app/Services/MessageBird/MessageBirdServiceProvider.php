<?php

namespace App\Services\MessageBird;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MessageBirdServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap application services
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(MessageBird::class, fn (Application $app) => new MessageBird($app['config']['services']['messagebird']['key']));
    }
}
