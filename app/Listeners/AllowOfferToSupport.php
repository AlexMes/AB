<?php

namespace App\Listeners;

use App\Events\OfferAllowed;
use App\User;

class AllowOfferToSupport
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle(OfferAllowed $event)
    {
        foreach (
                User::query()->whereIn('role', [User::SUPPORT,User::HEAD,User::DESIGNER])
                    ->whereBranchId($event->user->branch_id)
                    ->get() as $user
            ) {
            if (!$user->allowedOffers->contains($event->offer)) {
                $user->allowedOffers()->attach($event->offer);
            }
        }
    }
}
