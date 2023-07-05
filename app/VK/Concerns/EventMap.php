<?php

namespace App\VK\Concerns;

use App\VK\Events\Profiles\Creating as ProfileCreating;
use App\VK\Listeners\Profiles\SetUserId;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Trait EventMap
 *
 * @package App\VK\Concerns
 *
 * @mixin \Illuminate\Support\ServiceProvider
 */
trait EventMap
{
    /**
     * @var array
     */
    protected $events = [
        ProfileCreating::class => [
            SetUserId::class,
        ],
    ];

    /**
     * Register events & listeners.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     *
     */
    protected function registerEvents()
    {
        $dispatcher = $this->app->make(Dispatcher::class);

        foreach ($this->events as $event => $listeners) {
            foreach ($listeners as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }
    }
}
