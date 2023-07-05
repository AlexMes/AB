<?php

namespace App\CRM\Concenrs;

use Illuminate\Contracts\Events\Dispatcher;

trait EventMap
{

    /**
     * Event to listener map
     *
     * @var array
     */
    protected $events = [];

    /**
     * Register events & listeners.
     *
     * @return void
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
