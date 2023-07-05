<?php

namespace App\Deluge\Concenrs;

use App\Deluge\Events\Accounts;
use App\Deluge\Events\Apps;
use App\Deluge\Events\Campaigns;
use App\Deluge\Events\Unity;
use App\Deluge\Listeners\BindMissedLeads;
use App\Deluge\Listeners\DetermineCampaignUtmKey;
use App\Deluge\Listeners\LogModerationStatus;
use App\Deluge\Listeners\NotifyAppStatus;
use App\Deluge\Listeners\RefreshUnityData;
use Illuminate\Contracts\Events\Dispatcher;

trait EventMap
{

    /**
     * Event to listener map
     *
     * @var array
     */
    protected $events = [
        Campaigns\Saving::class => [
            DetermineCampaignUtmKey::class,
        ],
        Campaigns\Created::class => [
            BindMissedLeads::class,
        ],
        Accounts\Saved::class => [
            LogModerationStatus::class,
        ],
        Apps\Saved::class => [
            NotifyAppStatus::class,
        ],
        Unity\Organizations\Created::class => [
            RefreshUnityData::class,
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
