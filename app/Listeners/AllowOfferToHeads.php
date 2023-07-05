<?php

namespace App\Listeners;

use App\Events\OfferAllowed;
use App\User;

class AllowOfferToHeads
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
        if ($event->user->isBuyer() || $event->user->isTeamLead()) {
            foreach (
                User::query()->select('id')
                    ->with(['allowedOffers'])
                    ->teammates($event->user->id)
                    ->whereIn('role', [User::HEAD, User::TEAMLEAD])
                    ->whereBranchId($event->user->branch_id)
                    ->whereNotNull('branch_id')
                    ->get() as $user
            ) {
                if (!$user->allowedOffers->contains($event->offer)) {
                    $user->allowedOffers()->attach($event->offer);
                }
            }
        }
    }
}
