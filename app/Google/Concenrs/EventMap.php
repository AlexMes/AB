<?php

namespace App\Google\Concenrs;

use App\Events\GoogleSheetCreated;
use App\Listeners\Google\DispatchSheetConfiguration;
use Illuminate\Contracts\Events\Dispatcher;

trait EventMap
{

    /**
     * Event to listener map
     *
     * @var array
     */
    protected $events = [
        GoogleSheetCreated::class => [
            DispatchSheetConfiguration::class
        ],
    ];

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
