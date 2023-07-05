<?php

namespace App\Listeners;

use App\Events\OfferAllowed;
use App\User;

class AllowOfferToBuyers
{
    /**
     * Handle the event.
     *
     * @param OfferAllowed $event
     *
     * @return void
     */
    public function handle(OfferAllowed $event)
    {
        if ($event->user->isBranchHead() && $event->user->branch_id === 25) {
            foreach (
                User::query()->select('id')
                    ->with(['allowedOffers'])
                    ->whereIn('role', [User::BUYER])
                    ->whereBranchId($event->user->branch_id)
                    ->whereNotNull('branch_id')
                    ->whereIn('name', ['BULL', 'DRAGO', 'BANT'])
                    ->get() as $user
            ) {
                if (!$user->allowedOffers->contains($event->offer)) {
                    $user->allowedOffers()->attach($event->offer);
                }
            }
        }
    }
}
