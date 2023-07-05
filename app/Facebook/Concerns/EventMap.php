<?php

namespace App\Facebook\Concerns;

use App\Events\AdDisapproved;
use App\Facebook\Events\Ads\Saved;
use App\Facebook\Events\Campaigns\CampaignUpdated;
use App\Facebook\Events\Profiles\Banned;
use App\Facebook\Events\Profiles\Restored;
use App\Facebook\Listeners\CheckAdEffectiveStatus;
use App\Facebook\Listeners\CheckAdStatus;
use App\Facebook\Listeners\LogAdStatus;
use App\Facebook\Listeners\LogPaymentFail;
use App\Facebook\Listeners\MarkAccountAdDisabled;
use App\Facebook\Listeners\ResolvePageId;
use App\Facebook\Notifications\Accounts\AdvertisingDisabled;
use App\Facebook\Notifications\Campaigns\Updated;
use App\Listeners\DetermineDesignerId;
use App\Listeners\DetermineDomainId;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Trait EventMap
 *
 * @package App\Facebook\Concerns
 *
 * @mixin \Illuminate\Support\ServiceProvider
 */
trait EventMap
{
    /**
     * All of HR section event / listener mappings.
     *
     * @var array
     */
    protected $events = [
        Banned::class => [
            \App\Facebook\Notifications\Profiles\Banned::class
        ],
        Restored::class => [
            \App\Facebook\Notifications\Profiles\Restored::class,
        ],
        Saved::class => [
            ResolvePageId::class,
            DetermineDomainId::class,
            DetermineDesignerId::class,
            CheckAdEffectiveStatus::class,
        ],
        \App\Facebook\Events\Accounts\Updated::class => [
            \App\Facebook\Notifications\Accounts\Updated::class,
            AdvertisingDisabled::class,
            LogPaymentFail::class,
        ],
        \App\Facebook\Events\Adsets\Updated::class => [
            \App\Facebook\Notifications\Adsets\Updated::class
        ],
        CampaignUpdated::class => [
            Updated::class
        ],
        \App\Facebook\Events\Ads\Updated::class => [
            CheckAdStatus::class,
            \App\Facebook\Notifications\Ads\Updated::class,
        ],
        \App\Facebook\Events\Accounts\Updating::class => [
            \App\Facebook\Listeners\SetAccountBannedAt::class,
            \App\Facebook\Listeners\SetGroupId::class,
        ],
        \App\Facebook\Events\Profiles\Updating::class => [
            \App\Facebook\Listeners\SyncAccountGroup::class,
        ],
        \App\Facebook\Events\Accounts\Creating::class => [
            \App\Facebook\Listeners\SetGroupId::class,
        ],
        AdDisapproved::class => [
            LogAdStatus::class,
            MarkAccountAdDisabled::class,
        ],
        \App\Facebook\Events\PaymentFails\Created::class => [
            \App\Facebook\Notifications\PaymentFails\Created::class,
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
